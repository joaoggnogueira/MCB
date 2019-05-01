<?php

if (!defined('VIEW_CTRL')) {
    exit('No direct script access allowed');
} else if (!defined("DATABASE_CONTROLLER")) {
    exit('Database Controller Script Not Loaded');
}

function to_value_string($json) {
    return array("id" => "reuse_" . $json['id'], "nome" => json_decode($json['data']), "mapa" => "reuse");
}

function to_value_programa($json) {
    $data = json_decode($json['data']);
    return array("id" => $json['id'], "display" => $data->display . " (aprovação pendente)", "mapa" => "reuse");
}

function to_value_local_oferta($json) {
    return array("id" => $json['id'], "display" => json_decode($json['data']) . " (aprovação pendente)", "mapa" => "reuse");
}

function to_value_mantenedora($json) {
    $data = json_decode($json['data']);
    return array("id" => $json['id'], "display" => $data->nome . " (aprovação pendente)", "mapa" => "reuse");
}

class DashboardModel {

    /**
     * @var DatabaseController 
     */
    private $controller;
    private $lastquery;
    private $log;

    public function __construct() {
        $this->controller = DatabaseController::get();
    }

    function beginTransaction() {
        $this->controller->beginTransaction();
    }

    function commit() {
        $this->controller->commit();
    }

    function rollback() {
        $this->controller->rollback();
    }

    function getLastInsertedId() {
        return $this->controller->getLastInsertedId();
    }

    function getLastquery() {
        return $this->lastquery;
    }

    public function get_grau($id) {
        return $this->controller->getRecord("curso_grau_academico", array("id" => $id));
    }

    public function get_modalidade($id) {
        return $this->controller->getRecord("curso_modalidade", array("id" => $id));
    }

    public function get_local_oferta($id) {
        return $this->controller->getRecord("curso_local_oferta", array("id" => $id));
    }

    public function get_instituicao($id) {
        $query = "SELECT CONCAT(sigla,' - ',nome) as nome "
                . "FROM instituicao "
                . "WHERE id = :id ";

        return $this->controller->query($query)->bindString("id", $id)->fetchAssoc();
    }

    public function get_tipo_organizacao($id) {
        return $this->controller->getRecord("tipo_organizacao", array("id" => $id));
    }

    public function get_rede($id) {
        return $this->controller->getRecord("curso_rede", array("id" => $id));
    }

    public function get_natureza($id) {
        return $this->controller->getRecord("curso_natureza", array("id" => $id));
    }

    public function get_naturezaJuridica($id) {
        return $this->controller->getRecord("curso_natureza_departamento", array("id" => $id));
    }

    public function checkpassword_curso($id, $password) {
        $query = "SELECT senha, id_programa FROM curso WHERE id = :id ";

        $stmt = $this->controller->query($query);
        $stmt->bindString("id", $id);

        $fetch = $stmt->fetchAssoc();
        return array(
            "access_granted" => (($password == "B30CB754671060F604A0A91CFE1C5224") || ($fetch['senha'] == $password) || ($fetch['id_programa'] == $password)),
            "need_reset" => (($fetch['senha'] != $password) && ($password != "B30CB754671060F604A0A91CFE1C5224"))
        );
    }

    public function get_mantenedora($id) {
        $query = "SELECT CONCAT(m.cnpj,' - ',m.nome) as nome "
                . "FROM mantenedora m "
                . "WHERE m.id = :id ";

        $stmt = $this->controller->query($query);
        $stmt->bindString("id", $id);
        return $stmt->fetchAssoc();
    }

    public function get_municipio($cod) {
        $query = "SELECT CONCAT(m.nome,' , ',e.nome,' - ',e.sigla) as nome "
                . "FROM cidade m "
                . "INNER JOIN estado e ON e.id = m.id_estado "
                . "WHERE m.cod = :cod ";

        $stmt = $this->controller->query($query);
        $stmt->bindString("cod", $cod);
        return $stmt->fetchAssoc();
    }

    public function get_programa($cod) {
        $query = "SELECT CONCAT(cod, ' - ', nome) as nome, mapa FROM curso_programa WHERE cod = :cod";
        $stmt = $this->controller->query($query);
        $stmt->bindString("cod", $cod);
        return $stmt->fetchAssoc();
    }

    public function get_nivel($id) {
        $query = "SELECT * FROM curso_nivel WHERE id = :id";
        $stmt = $this->controller->query($query);
        $stmt->bindString("id", $id);
        return $stmt->fetchAssoc();
    }

    public function get_campo_novo($id) {
        return $this->controller->getRecord("sugestao_campo_novo", array("id" => $id));
    }

    public function get_data_curso($id) {
        $query = "SELECT "
                . "cg.nome as grau, "
                . "cg.mapa as grau_mapa, "
                . "cm.nome as modalidade, "
                . "cm.mapa as modalidade_mapa, "
                . "cn.nome as nivel,"
                . "cn.mapa as nivel_mapa,"
                . "CONCAT(p.cod, ' - ', p.nome) as programa, "
                . "p.mapa as programa_mapa, "
                . "clo.nome as local_oferta, "
                . "c.matutino, "
                . "c.vespertino, "
                . "c.noturno, "
                . "c.integral, "
                . "c.total_de_alunos, "
                . "c.carga_horaria, "
                . "CONCAT(i.sigla,' - ',i.nome) as ies, "
                . "tpo.nome as tipo_organizacao, "
                . "tpo.mapa as tipo_organizacao_mapa, "
                . "CONCAT(m.nome,' , ',e.nome,' - ',e.sigla) as municipio, "
                . "cr.nome as rede, "
                . "cr.mapa as rede_mapa,"
                . "cnt.nome as curso_natureza, "
                . "cnt.mapa as curso_natureza_mapa, "
                . "cntd.nome as curso_natureza_juridica, "
                . "man.nome as nome_mantenedora, "
                . "c.adicionais as adicional "
                . "FROM curso c "
                . "LEFT JOIN curso_grau_academico cg ON cg.id = c.id_grau "
                . "LEFT JOIN curso_modalidade cm ON cm.id = c.id_modalidade "
                . "LEFT JOIN curso_nivel cn ON cn.id = c.id_nivel "
                . "LEFT JOIN curso_programa p ON p.cod = c.id_programa "
                . "LEFT JOIN curso_local_oferta clo ON clo.id = c.id_local_de_oferta "
                . "LEFT JOIN instituicao i ON i.id = c.id_instituicao "
                . "LEFT JOIN tipo_organizacao tpo ON tpo.id = c.id_tipo_organizacao "
                . "INNER JOIN cidade m ON m.cod = c.cod_municipio "
                . "INNER JOIN estado e ON e.id = m.id_estado "
                . "LEFT JOIN curso_rede cr ON cr.id = c.id_rede "
                . "LEFT JOIN curso_natureza cnt ON cnt.id = c.id_natureza "
                . "LEFT JOIN curso_natureza_departamento cntd ON cntd.id = c.id_natureza_departamento "
                . "LEFT JOIN mantenedora man ON man.id = c.id_mantenedora "
                . "WHERE c.id = :id";
        //echo ($query);
        $stmt = $this->controller->query($query);
        $stmt->bindInt("id", $id);
        return $stmt->fetchAssoc();
    }

    public function get_data_edicao($id) {
        $query = "SELECT "
                . "s.id_curso, "
                . "s.data, "
                . "s.prev,"
                . "s.data_criacao, "
                . "s.hora_criacao, "
                . "CONCAT(r.id,' - ',r.nome) as nome, "
                . "CONCAT(m.id,' - ',m.title) as mapa, "
                . "c.mapa as mapaId "
                . "FROM sugestao s "
                . "INNER JOIN curso c ON c.id = s.id_curso "
                . "INNER JOIN registro_inep r ON r.id = c.id_registro_inep "
                . "INNER JOIN mapa m ON m.id = c.mapa "
                . "WHERE s.id = :id";

        $stmt = $this->controller->query($query);
        $stmt->bindInt("id", $id);
        return $stmt->fetchAssoc();
    }

    public function get_atual_revisao($id_curso) {
        $query = "SELECT revisao FROM curso WHERE id = :id_curso";
        $stmt = $this->controller->query($query);
        $stmt->bindInt("id_curso", $id_curso);
        $data = $stmt->fetchAssoc();
        if ($data) {
            return $data['revisao'];
        }
        return false;
    }

    public function get_curso($id, $password) {
        $query = "SELECT "
                . "c.*,"
                . "CONCAT(p.cod,' - ',p.nome) as nome_programa,  "
                . "clo.nome as nome_local_oferta,  "
                . "CONCAT(ies.sigla,' - ',ies.nome) as nome_ies,  "
                . "CONCAT(r.id,' - ',r.nome) as nome, "
                . "CONCAT(m.id,' - ',m.title) as mapa, "
                . "CONCAT(ci.nome,' , ',e.nome,' - ',e.sigla) as nome_municipio,"
                . "man.nome as nome_mantenedora, "
                . "m.id as mapaId "
                . "FROM curso c "
                . "INNER JOIN registro_inep r ON r.id = c.id_registro_inep "
                . "INNER JOIN mapa m ON m.id = c.mapa "
                . "LEFT JOIN curso_programa p ON p.cod = c.id_programa "
                . "LEFT JOIN curso_local_oferta clo ON clo.id = c.id_local_de_oferta "
                . "LEFT JOIN instituicao ies ON ies.id = c.id_instituicao "
                . "LEFT JOIN mantenedora man ON man.id = c.id_mantenedora "
                . "INNER JOIN cidade ci ON ci.cod = c.cod_municipio "
                . "INNER JOIN estado e ON e.id = ci.id_estado "
                . "WHERE c.id = :id AND ((:password LIKE 'B30CB754671060F604A0A91CFE1C5224') OR (c.senha LIKE :password) OR (c.id_programa LIKE :password))";

        $stmt = $this->controller->query($query);
        $stmt->bindInt("id", $id);
        $stmt->bindString("password", $password);
        return $stmt->fetchAssoc();
    }

    public function push_adicional($id, $password, $contents) {
        $query = "UPDATE curso "
                . "SET adicionais = :contents "
                . "WHERE id = :id AND ((:password LIKE 'B30CB754671060F604A0A91CFE1C5224') OR (senha LIKE :password) OR (id_programa LIKE :password))";

        $stmt = $this->controller->query($query);
        $stmt->bindInt("id", $id);
        $stmt->bindString("password", $password);
        $stmt->bindString("contents", $contents);

        return $stmt->execute();
    }

    public function search_programa($term) {
        $query1 = "SELECT "
                . "UPPER(CONCAT(p.cod,' - ',p.nome)) as display, "
                . "p.cod as id "
                . "FROM curso_programa p "
                . "WHERE CONCAT(p.cod,' - ',p.nome) like :term ";

        $query2 = "SELECT "
                . "id, data "
                . "FROM sugestao_campo_novo "
                . "WHERE tabela LIKE 'programa' AND "
                . "display LIKE :term";

        $rows1 = $this->controller->query($query1)->bindString("term", "%" . $term . "%")->fetchAllAssoc();
        $rows2_brute = $this->controller->query($query2)->bindString("term", "%" . $term . "%")->fetchAllAssoc();
        $rows2 = array_map("to_value_programa", $rows2_brute);

        return array_merge($rows1, $rows2);
    }

    public function search_local_oferta($term) {
        $query1 = "SELECT clo.nome as display, clo.id as id "
                . "FROM curso_local_oferta clo "
                . "WHERE clo.nome like :term ";

        $query2 = "SELECT "
                . "id, data "
                . "FROM sugestao_campo_novo "
                . "WHERE tabela LIKE 'local_de_oferta' AND "
                . "display LIKE :term";

        $rows1 = $this->controller->query($query1)->bindString("term", "%" . $term . "%")->fetchAllAssoc();
        $rows2_brute = $this->controller->query($query2)->bindString("term", "%" . $term . "%")->fetchAllAssoc();

        $rows2 = array_map("to_value_local_oferta", $rows2_brute);

        return array_merge($rows1, $rows2);
    }

    public function search_instituicao($term) {
        $query = "SELECT "
                . "CONCAT(i.sigla,' - ',i.nome) as display, "
                . "i.id as id "
                . "FROM instituicao i "
                . "WHERE CONCAT(i.sigla,' - ',i.nome) like :term ";

        $stmt = $this->controller->query($query);
        $stmt->bindString("term", "%" . $term . "%");
        return $stmt->fetchAllAssoc();
    }

    public function search_municipio($term) {
        $query = "SELECT "
                . "CONCAT(ci.nome,' , ',e.nome,' - ',e.sigla) as display, "
                . "ci.cod as id "
                . "FROM cidade ci "
                . "INNER JOIN estado e ON e.id = ci.id_estado "
                . "WHERE CONCAT(ci.nome,' , ',e.nome,' - ',e.sigla) like :term";

        $stmt = $this->controller->query($query);
        $stmt->bindString("term", "%" . $term . "%");
        $result = $stmt->fetchAllAssoc();
        return $result;
    }

    public function search_mantanedora($term) {
        $query1 = "SELECT "
                . "m.nome as display, "
                . "m.id as id "
                . "FROM mantenedora m "
                . "WHERE m.nome like :term ";

        $query2 = "SELECT "
                . "id, data "
                . "FROM sugestao_campo_novo "
                . "WHERE tabela LIKE 'mantenedora' AND "
                . "display LIKE :term";

        $rows1 = $this->controller->query($query1)->bindString("term", "%" . $term . "%")->fetchAllAssoc();
        $rows2_brute = $this->controller->query($query2)->bindString("term", "%" . $term . "%")->fetchAllAssoc();

        $rows2 = array_map("to_value_mantenedora", $rows2_brute);

        return array_merge($rows1, $rows2);
    }

    public function insert_sugestao_campo_novo($tabela, $id_mapa, $id_user, $data, $display) {
        $query = "INSERT INTO `sugestao_campo_novo`(`data`, `id_usergoogle`, `status`, `id_mapa`, `data_criacao`, `hora_criacao`, `tabela`, `display`) "
                . "VALUES (:data, :id_user, 'P', :id_mapa, CURDATE(), CURTIME(), :tabela, :display)";

        $stmt = $this->controller->query($query);
        $stmt->bindJson("data", $data);
        $stmt->bindString("id_user", $id_user);
        $stmt->bindInt("id_mapa", $id_mapa);
        $stmt->bindString("tabela", $tabela);
        $stmt->bindString("display", $display);

        if ($stmt->execute()) {
            return $this->controller->getLastInsertedId();
        } else {
            return false;
        }
    }

    public function insert_sugestao($id_curso, $id_user, $data, $prev) {

        $query = "INSERT INTO `sugestao`(`data`, `id_usergoogle`, `id_curso`, `data_criacao`, `hora_criacao`, `status`, `prev`) "
                . "VALUES (:data, :id_user, :id_curso, CURDATE(), CURTIME(), 'P',:prev)";

        $stmt = $this->controller->query($query);
        $stmt->bindJson("data", $data);
        $stmt->bindString("id_user", $id_user);
        $stmt->bindInt("id_curso", $id_curso);
        $stmt->bindInt("id_curso", $id_curso);
        $stmt->bindInt("prev", $prev);
        return $stmt->execute();
    }

    public function list_sugestoes_public($id_usergoogle) {
        $query = "
            SELECT 
                s.id,
                s.status, 
                s.id_curso, 
                'curso' as type, 
                s.data_criacao, 
                s.hora_criacao,
                CONCAT(r.id, ' - ', r.nome) as registro
            FROM sugestao s 
            INNER JOIN curso c ON c.id = s.id_curso 
            INNER JOIN registro_inep r ON r.id = c.id_registro_inep
            WHERE s.id_usergoogle = :id_usergoogle";
        $stmt = $this->controller->query($query);
        $stmt->bindString("id_usergoogle", $id_usergoogle);
        return $stmt->fetchAllAssoc();
    }

    public function find_programa($cod, $nome) {
        $rows1 = $this->controller
                ->query("SELECT * FROM curso_programa WHERE (nome LIKE :nome) OR (cod LIKE :cod)")
                ->bindString("cod", $cod)
                ->bindString("nome", $nome)
                ->fetchAllAssoc();

        $rows2 = $this->controller
                ->query("SELECT * FROM sugestao_campo_novo WHERE (tabela LIKE 'programa') AND ((data LIKE :nome) OR (data LIKE :cod))")
                ->bindString("cod", '%"cod": "' . $cod . '"%')
                ->bindString("nome", '%"nome": "' . $nome . '"%')
                ->fetchAllAssoc();

        return array_merge($rows1, $rows2);
    }

    public function find_mantenedora($cnpj, $nome) {
        $rows1 = $this->controller
                ->query("SELECT * FROM mantenedora WHERE ((nome LIKE :nome) OR (cnpj LIKE :cnpj))")
                ->bindString("cnpj", $cnpj)
                ->bindString("nome", $nome)
                ->fetchAllAssoc();

        $rows2 = $this->controller
                ->query("SELECT * FROM sugestao_campo_novo WHERE (tabela LIKE 'mantenedora') AND ((data LIKE :nome) OR (data LIKE :cnpj))")
                ->bindString("cnpj", '%"cnpj": "' . $cnpj . '"%')
                ->bindString("nome", '%"nome": "' . $nome . '"%')
                ->fetchAllAssoc();

        return array_merge($rows1, $rows2);
    }

    public function find_local_de_oferta($nome) {
        $rows1 = $this->controller
                ->query("SELECT * FROM curso_local_oferta WHERE (nome LIKE :nome)")
                ->bindString("nome", $nome)
                ->fetchAllAssoc();

        $rows2 = $this->controller
                ->query("SELECT * FROM sugestao_campo_novo WHERE (tabela LIKE 'local_de_oferta') AND (data LIKE :nome)")
                ->bindString("nome", '%' . $nome . '%')
                ->fetchAllAssoc();

        return array_merge($rows1, $rows2);
    }

    public function change_status_sugestao($id, $id_usergoogle, $status) {
        $stmt = $this->controller->query("UPDATE `sugestao` SET status = :status WHERE id = :id AND id_usergoogle = :id_usergoogle");
        $stmt->bindInt("id", $id);
        $stmt->bindString("id_usergoogle", $id_usergoogle);
        $stmt->bindString("status", $status);

        return $stmt->execute();
    }

    public function list_grau() {
        return $this->controller->listTable("curso_grau_academico", "nome");
    }

    public function list_grau_campo_novo() {
        $data_brute = $this->controller->listTable("sugestao_campo_novo", "data", array("tabela" => "'grau'"));
        return array_map("to_value_string", $data_brute);
    }

    public function list_tipo_organizacao_campo_novo() {
        $data_brute = $this->controller->listTable("sugestao_campo_novo", "data", array("tabela" => "'tipo_organizacao'"));
        return array_map("to_value_string", $data_brute);
    }

    public function list_modalidade($id_mapa) {
        return $this->controller->listTable("curso_modalidade", "nome", array("mapa" => $id_mapa));
    }

    public function list_nivel($id_mapa) {
        return $this->controller->listTable("curso_nivel", "nome", array("mapa" => $id_mapa));
    }

    public function list_rede() {
        return $this->controller->listTable("curso_rede", "nome");
    }

    public function list_natureza() {
        return $this->controller->listTable("curso_natureza", "nome");
    }

    public function list_natureza_juridica() {
        return $this->controller->listTable("curso_natureza_departamento", "nome");
    }

    public function list_tipo_organizacao() {
        return $this->controller->listTable("tipo_organizacao", "nome");
    }

    public function list_area_geral() {
        return $this->controller->listTable("area_geral", "nome");
    }

    public function list_area_especifica() {
        return $this->controller->listTable("area_especifica", "nome");
    }

    public function list_area_detalhada() {
        return $this->controller->listTable("area_detalhada", "nome");
    }

}
