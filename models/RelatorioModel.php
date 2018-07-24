<?PHP

if (!defined('VIEW_CTRL')) {
    exit('No direct script access allowed');
} else if (!defined("DATABASE_CONTROLLER")) {
    exit('Database Controller Script Not Loaded');
}

class RelatorioModel {

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

    function getLastInsertedId() {
        return $this->controller->getLastInsertedId();
    }

    function getLastquery() {
        return $this->lastquery;
    }

    public function listGrau() {
        return $this->controller->listTable("curso_grau_academico", "nome");
    }

    public function listRede() {
        return $this->controller->listTable("curso_rede", "nome");
    }

    public function listModalidade() {
        return $this->controller->listTable("curso_modalidade", "nome");
    }

    public function listNatureza() {
        return $this->controller->listTable("curso_natureza", "nome");
    }

    public function listNaturezaDepartamento() {
        return $this->controller->listTable("curso_natureza_departamento", "nome");
    }

    public function listNivel() {
        return $this->controller->listTable("curso_nivel", "nome");
    }

    public function listPrograma() {
        return $this->controller->listTable("curso_programa", "nome");
    }

    public function listEstado() {
        return $this->controller->listTable("estado", "nome");
    }

    public function listRegiao() {
        return $this->controller->listTable("regiao", "nome");
    }

    public function listTipoOrganizacao() {
        return $this->controller->listTable("tipo_organizacao", "nome");
    }

    public function listInstituicoes() {
        return $this->controller->listTable("instituicao", "sigla");
    }

    public function listConfiguracoes() {
        $query = "SELECT id,nome,json,data_hora FROM relatorio";
        $stmt = $this->controller->query($query);
        return $stmt->fetchAll();
    }

    public function saveConfiguracoes($rotulo, $json) {

        $query = "INSERT INTO relatorio(nome,json,ip,data_hora) VALUES (:nome,:json,:ip,NOW())";

        $stmt = $this->controller->query($query);

        $stmt->bindString("nome", $rotulo);
        $stmt->bindString("json", $json);
        $stmt->bindCurrentIp("ip");

        return $stmt->execute();
    }

    public function getCursoDetails($id) {
        $query = "SELECT 
                c.`matutino` as 'eh_matutino',
                c.`vespertino` as 'eh_vespertino',
                c.`noturno` as 'eh_noturno',
                c.`integral` as 'eh_integral',
                ri.nome as 'nome_do_curso',
                ri.inicio_funcionamento as 'inicio_do_funcionamento',
                m.nome as 'nome_do_municipio',
                m.latitude as 'latitude_municipio',
                m.longitude as 'longitude_municipio',
                m.cod as 'codigo_municipio',
                e.nome as 'nome_do_estado',
                e.sigla as 'sigla_do_estado',
                r.nome as 'nome_da_regiao',
                i.id as 'id_instituicao',
                i.nome as 'nome_da_instituicao',
                i.sigla as 'sigla_da_instituicao',
                cg.nome as 'grau_academico',
                cm.nome as 'modalidade',
                cn.nome as 'nivel',
                o.nome as 'tipo_da_organizacao',
                cp.nome as 'nome_do_programa',
                cp.cod as 'codigo_do_programa',
                ad.nome as 'area_detalhada',
                ae.nome as 'area_especifica',
                ag.nome as 'area_geral',
                cr.nome as 'rede',
                cnpr.nome as 'natureza_privada',
                cnpu.nome as 'natureza_publica'
                FROM `curso` c
                LEFT JOIN `registro_inep` ri ON ri.`id` = c.`id_registro_inep`
                LEFT JOIN `cidade` m ON m.`cod` = c.`cod_municipio` 
                LEFT JOIN `estado` e ON e.`id` = m.`id_estado`
                LEFT JOIN `regiao` r ON r.`id` = e.`id_regiao`
                LEFT JOIN `instituicao` i ON i.`id` = c.`id_instituicao` 
                LEFT JOIN `curso_grau_academico` cg ON cg.`id` = c.`id_grau` 
                LEFT JOIN `curso_modalidade` cm ON cm.`id` = c.`id_modalidade` 
                LEFT JOIN `curso_nivel` cn ON cn.`id` = c.`id_nivel` 
                LEFT JOIN `tipo_organizacao` o ON o.`id` = c.`id_tipo_organizacao` 
                LEFT JOIN `curso_programa` cp ON cp.`cod` = c.`id_programa`
                LEFT JOIN `area_detalhada` ad ON ad.`id` = cp.`id_area_detalhada`
                LEFT JOIN `area_especifica` ae ON ae.`id` = ad.`id_area_especifica`
                LEFT JOIN `area_geral` ag ON ag.`id` = ae.`id_area_geral`
                LEFT JOIN `curso_rede` cr ON cr.`id` = c.`id_rede`
                LEFT JOIN `curso_natureza` cnpr ON cnpr.`id` = c.`id_natureza`
                LEFT JOIN `curso_natureza_departamento` cnpu ON cnpu.`id` = c.`id_natureza_departamento`
                WHERE c.id = :id";
        $stmt = $this->controller->query($query);

        $stmt->bindInt("id", $id);

        return $stmt->fetchAssoc();
    }

    public function getConfiguracoes($id) {
        $query = "SELECT id,nome,json,data_hora FROM relatorio WHERE id = :id";
        $stmt = $this->controller->query($query);

        $stmt->bindInt("id", $id);

        return $stmt->fetchAssoc();
    }

    public function listCursosByMunicipio($cod_mun, $filters) {
        $query = "SELECT "
                . "c.id as id "
                . ",ri.nome as nome "
                . ",i.sigla as instituicao "
                . ",i.nome as nome_instituicao "
                . "FROM curso c "
                . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                . "INNER JOIN estado e ON e.id = m.id_estado "
                . "INNER JOIN registro_inep ri ON ri.id = c.id_registro_inep "
                . "INNER JOIN instituicao i ON i.id = c.id_instituicao ";

        $stmt = $this->append_filter_to_query($filters, $query, false, " c.cod_municipio = :cod_mun ", false);
        $stmt->bindString("cod_mun", $cod_mun);
        return $stmt->fetchAll();
    }

    public function listCursosByEstado($id_estado, $filters) {
        $query = "SELECT "
                . "c.id as id "
                . ",ri.nome as nome "
                . ",i.sigla as instituicao "
                . ",i.nome as nome_instituicao "
                . "FROM curso c "
                . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                . "INNER JOIN estado e ON e.id = m.id_estado "
                . "INNER JOIN registro_inep ri ON ri.id = c.id_registro_inep "
                . "INNER JOIN instituicao i ON i.id = c.id_instituicao ";

        $stmt = $this->append_filter_to_query($filters, $query, false, " m.id_estado = :id_estado ", false);
        $stmt->bindString("id_estado", $id_estado);
        return $stmt->fetchAll();
    }

    public function listCursosByRegiao($id_regiao, $filters) {
        $query = "SELECT "
                . "c.id as id "
                . ",ri.nome as nome "
                . ",i.sigla as instituicao "
                . ",i.nome as nome_instituicao "
                . "FROM curso c "
                . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                . "INNER JOIN estado e ON e.id = m.id_estado "
                . "INNER JOIN registro_inep ri ON ri.id = c.id_registro_inep "
                . "INNER JOIN instituicao i ON i.id = c.id_instituicao ";

        $stmt = $this->append_filter_to_query($filters, $query, false, " e.id_regiao = :id_regiao ", false);
        $stmt->bindString("id_regiao", $id_regiao);
        return $stmt->fetchAll();
    }

    public function listMarkersMunicipios($filters) {

        $query = "SELECT "
                . "COUNT(c.cod_municipio) as total "
                . ",m.longitude as lng "
                . ",m.latitude as lat "
                . ",m.cod as cod "
                . ",m.nome as nome "
                . ",e.sigla as uf "
                . "FROM curso c "
                . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                . "INNER JOIN estado e ON e.id = m.id_estado ";

        $stmt = $this->append_filter_to_query($filters, $query, "c.cod_municipio", false, false);
        return $stmt->fetchAll();
    }

    public function listMarkersEstado($filters) {

        $query = "SELECT "
                . "COUNT(c.cod_municipio) as total "
                . ",e.longitude as lng "
                . ",e.latitude as lat "
                . ",e.id as cod "
                . ",e.nome as nome "
                . ",e.sigla as uf "
                . "FROM curso c "
                . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                . "INNER JOIN estado e ON e.id = m.id_estado ";

        $stmt = $this->append_filter_to_query($filters, $query, "e.id", false, false);
        return $stmt->fetchAll();
    }

    public function listMarkersRegiao($filters) {

        $query = "SELECT "
                . "COUNT(c.cod_municipio) as total "
                . ",r.longitude as lng "
                . ",r.latitude as lat "
                . ",r.id as cod "
                . ",r.nome as nome "
                . ",'' as uf "
                . "FROM curso c "
                . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                . "INNER JOIN estado e ON e.id = m.id_estado "
                . "INNER JOIN regiao r ON r.id = e.id_regiao ";

        $stmt = $this->append_filter_to_query($filters, $query, "e.id_regiao", false, false);
        return $stmt->fetchAll();
    }

    function totais($table, $cod, $filters, $markerType) {
        $query = "";
        $order_by = "";
        if ($table == "grau") {
            $query .= "SELECT cg.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_grau_academico cg ON cg.id = c.id_grau ";
            $order_by = " c.id_grau ";
        } else if ($table == "rede") {
            $query .= "SELECT cr.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_rede cr ON cr.id = c.id_rede ";
            $order_by = " c.id_rede ";
        } else if ($table == "modalidade") {
            $query .= "SELECT cm.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_modalidade cm ON cm.id = c.id_modalidade ";
            $order_by = " c.id_modalidade ";
        } else if ($table == "natureza") {
            $query .= "SELECT cn.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_natureza cn ON cn.id = c.id_natureza ";
            $order_by = " c.id_natureza ";
        } else if ($table == "naturezadep") {
            $query .= "SELECT cnd.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_natureza_departamento cnd ON cnd.id = c.id_natureza_departamento ";
            $order_by = " c.id_natureza_departamento ";
        } else if ($table == "nivel") {
            $query .= "SELECT cn.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_nivel cn ON cn.id = c.id_nivel ";
            $order_by = " c.id_nivel ";
        } else if ($table == "programa") {
            $query .= "SELECT cp.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_programa cp ON cp.cod = c.id_programa ";
            $order_by = " c.id_programa ";
        } else if ($table == "tipoorganizacao") {
            $query .= "SELECT t.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN tipo_organizacao t ON t.id = c.id_tipo_organizacao ";
            $order_by = " c.id_tipo_organizacao ";
        } else if ($table == "enade") {
            $query .= "SELECT c.temp_faixa_enade as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado ";
            $order_by = " c.temp_faixa_enade ";
        } else if ($table == "estado") {
            $query .= "SELECT CONCAT(e.nome,' (',e.sigla,')') as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado ";
            $order_by = " e.id ";
        }

        if ($markerType == 0) {
            $stmt = $this->append_filter_to_query($filters, $query, $order_by, "c.cod_municipio = :cod_mun ","total DESC");
            $stmt->bindInt("cod_mun", $cod);
        } else if ($markerType == 1) {
            $stmt = $this->append_filter_to_query($filters, $query, $order_by, "m.id_estado = :id_estado ","total DESC");
            $stmt->bindInt("id_estado", $cod);
        } else if ($markerType == 2) {
            $stmt = $this->append_filter_to_query($filters, $query, $order_by, "e.id_regiao = :id_regiao ","total DESC");
            $stmt->bindInt("id_regiao", $cod);
        }

        return $stmt->fetchAllAssoc();
    }

    private function append_filter_to_query($filters, $query, $group_by, $adictional, $order_by) {

        $first = true;
        $this->log = array();
        if (isset($filters->instituicao)) {
            $query .= $this->append_filter($filters->instituicao, "c.id_instituicao", "instituicao", $first);
        }
        $query .= $this->append_filter($filters->grau, "c.id_grau", "grau", $first);
        $query .= $this->append_filter($filters->rede, "c.id_rede", "rede", $first);
        $query .= $this->append_filter($filters->modalidades, "c.id_modalidade", "modalidade", $first);
        $query .= $this->append_filter($filters->natureza, "c.id_natureza", "natureza", $first);
        $query .= $this->append_filter($filters->naturezadep, "c.id_natureza_departamento", "naturezadep", $first);
        $query .= $this->append_filter($filters->nivel, "c.id_nivel", "nivel", $first);
        $query .= $this->append_filter($filters->programa, "c.id_programa", "programa", $first);
        $query .= $this->append_filter($filters->tipoorganizacao, "c.id_tipo_organizacao", "tipoorganizacao", $first);
        $query .= $this->append_filter($filters->regiao, "e.id_regiao", "regiao", $first);
        $query .= $this->append_filter($filters->estado, "m.id_estado", "estado", $first);
        $query .= $this->append_filter($filters->enade, "c.temp_faixa_enade", "enade", $first);

        if ($adictional) {
            if ($first) {
                $query .= " WHERE ";
            } else {
                $query .= " AND ";
            }
            $query .= $adictional . " ";
        }
        if ($group_by) {
            $query .= " GROUP BY $group_by ";
        }
        if ($order_by) {
            $query .= " ORDER BY $order_by ";
        }
        $this->log = array($query);
        $stmt = $this->controller->query($query);
        if (isset($filters->instituicao)) {
            $this->bind_array_to_param($stmt, $filters->instituicao, "instituicao");
        }
        $this->bind_array_to_param($stmt, $filters->grau, "grau");
        $this->bind_array_to_param($stmt, $filters->rede, "rede");
        $this->bind_array_to_param($stmt, $filters->modalidades, "modalidade");
        $this->bind_array_to_param($stmt, $filters->natureza, "natureza");
        $this->bind_array_to_param($stmt, $filters->naturezadep, "naturezadep");
        $this->bind_array_to_param($stmt, $filters->nivel, "nivel");
        $this->bind_array_to_param($stmt, $filters->programa, "programa");
        $this->bind_array_to_param($stmt, $filters->tipoorganizacao, "tipoorganizacao");
        $this->bind_array_to_param($stmt, $filters->regiao, "regiao");
        $this->bind_array_to_param($stmt, $filters->estado, "estado");
        $this->bind_array_to_param($stmt, $filters->enade, "enade");


        return $stmt;
    }

    /**
     * @param array(string) $filter
     * @param string $id_name
     * @param string $param
     * @param boolean $first
     */
    private function append_filter($filter, $id_name, $param, &$first) {
        if (!isset($filter->all)) {
            $param = $this->filter_to_param($filter, $param);
            if ($first) {
                $first = false;
                return "WHERE $id_name IN ($param) ";
            } else {
                return "AND $id_name IN ($param) ";
            }
        }
        return "";
    }

    /**
     * 
     * @param array(string) $filter
     * @param string $param
     * @return string
     */
    private function filter_to_param($filter, $param) {

        $first = true;
        $query = "";
        foreach ($filter as $value) {
            if ($first) {
                $first = false;
            } else {
                $query .= ",";
            }
            $work = str_replace("-", "_", "" . $value);
            $query .= ":" . $param . "_" . $work;
        }
        return ($query);
    }

    /**
     * @param QueryDatabase $stmt
     * @param array(string) $filter
     * @param string $param
     */
    private function bind_array_to_param($stmt, $filter, $param) {
        if (!isset($filter->all)) {

            foreach ($filter as $value) {
                $work = str_replace("-", "_", "" . $value);
                $stmt->bindString($param . "_" . $work, $value);
                $this->log[] = "Definindo " . $param . "_" . $work . " como " . $value;
            }
        }
    }

    function getLog() {
        return $this->log;
    }

}
