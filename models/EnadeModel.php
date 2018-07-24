<?PHP

if (!defined('VIEW_CTRL')) {
    exit('No direct script access allowed');
} else if (!defined("DATABASE_CONTROLLER")) {
    exit('Database Controller Script Not Loaded');
}

class EnadeModel {

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

    public function getAvaliacoesCampus($id_inst) {
        $query = "SELECT "
                . "DISTINCT(a.cod_municipio) as cod_municipio,"
                . "CONCAT(c.nome,' (',e.sigla,')') as nome_municipio "
                . "FROM avaliacao_enade a "
                . "INNER JOIN cidade c ON c.cod = a.cod_municipio "
                . "INNER JOIN estado e ON e.id = c.id_estado "
                . "WHERE a.id_instituicao = :id_inst";

        $stmt = $this->controller->query($query);
        $stmt->bindInt("id_inst", $id_inst);
        return $stmt->fetchAllAssoc();
    }

    public function getAvaliacoesAno($id_inst, $cod_mun) {
        $query = "SELECT "
                . "DISTINCT(a.ano) as ano "
                . "FROM avaliacao_enade a "
                . "WHERE a.id_instituicao = :id_inst AND a.cod_municipio = :cod_mun";

        $stmt = $this->controller->query($query);
        $stmt->bindInt("id_inst", $id_inst);
        $stmt->bindInt("cod_mun", $cod_mun);
        return $stmt->fetchAllAssoc();
    }

    public function getAvaliacoes($id_area, $cod_mun, $ano, $id_inst) {
        $query = "SELECT * "
                . "FROM avaliacao_enade a "
                . "WHERE a.id_instituicao = :id_inst "
                . "AND a.cod_municipio = :cod_mun "
                . "AND a.ano = :ano "
                . "AND a.area_enade = :id_area";

        $stmt = $this->controller->query($query);
        $stmt->bindInt("id_inst", $id_inst);
        $stmt->bindInt("cod_mun", $cod_mun);
        $stmt->bindInt("ano", $ano);
        $stmt->bindInt("id_area", $id_area);
        return $stmt->fetchAssoc();        
    }
    
    public function getAvaliacoesArea($id_inst, $cod_mun, $ano) {
        $query = "SELECT ar.id,ar.nome "
                . "FROM area_enade ar "
                . "INNER JOIN avaliacao_enade av ON av.area_enade = ar.id "
                . "WHERE av.id_instituicao = :id_inst "
                . "AND av.cod_municipio = :cod_mun "
                . "AND av.ano = :ano";

        $stmt = $this->controller->query($query);
        $stmt->bindInt("id_inst", $id_inst);
        $stmt->bindInt("cod_mun", $cod_mun);
        $stmt->bindInt("ano", $ano);
        return $stmt->fetchAllAssoc();
    }

    //**Se a indução resultar em 2 casos, ambos serão desconsiderados, somente um resultado é possível
    public function inducaoAreaEnadeCurso($id_curso) {
        $query = "SELECT i.`id_area_enade` as id "
                . "FROM curso c "
                . "INNER JOIN `inducao_area_enade` i ON (c.id_programa = i.cod_programa AND c.id_grau = i.id_grau_academico) "
                . "WHERE c.id = :id_curso";

        $stmt = $this->controller->query($query);
        $stmt->bindInt("id_curso", $id_curso);
        $result = $stmt->fetchAllAssoc();
        if (count($result) === 1) {
            return $result[0]['id'];
        }
        return -1;
    }

}
