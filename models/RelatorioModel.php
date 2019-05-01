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

    public function getMapaInfo($id) {
        return $this->controller->getRecord("mapa", array("id" => $id));
    }

    public function listGrau($mapa) {
        return $this->controller->listTable("curso_grau_academico", "nome", array("mapa" => $mapa));
    }

    public function listRede($mapa) {
        return $this->controller->listTable("curso_rede", "nome", array("mapa" => $mapa));
    }

    public function listModalidade($mapa) {
        return $this->controller->listTable("curso_modalidade", "nome", array("mapa" => $mapa));
    }

    public function listNatureza($mapa) {
        return $this->controller->listTable("curso_natureza", "nome", array("mapa" => $mapa));
    }

    public function listNaturezaDepartamento() {
        return $this->controller->listTable("curso_natureza_departamento", "nome");
    }

    public function listNivel($mapa) {
        return $this->controller->listTable("curso_nivel", "nome", array("mapa" => $mapa));
    }

    public function listPrograma($mapa) {
        return $this->controller->listTable("curso_programa", "nome", array("mapa" => $mapa));
    }

    public function listEstado() {
        return $this->controller->listTable("estado", "nome");
    }

    public function listRegiao() {
        return $this->controller->listTable("regiao", "nome");
    }

    public function listTipoOrganizacao($mapa) {
        return $this->controller->listTable("tipo_organizacao", "nome", array("mapa" => $mapa));
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

    public function getMarkerDetails($id, $markerType) {
        $query = "";
        if ($markerType === 0) {
            $query = "SELECT "
                    . "c.`nome` as 'nome_municipio', "
                    . "c.`cod` as 'codigo_municipio', "
                    . "c.`latitude` as 'latitude', "
                    . "c.`longitude` as 'longitude', "
                    . "e.`nome` as 'nome_estado', "
                    . "e.`sigla` as 'sigla_estado', "
                    . "r.`nome` as 'nome_regiao', "
                    . "c.`populacao` as 'populacao' "
                    . "FROM cidade c "
                    . "INNER JOIN estado e ON e.id = c.id_estado "
                    . "INNER JOIN regiao r ON r.id = e.id_regiao "
                    . "WHERE c.cod = :id";
        } else if ($markerType === 1) {
            $query = "SELECT "
                    . "e.`nome` as 'nome_estado', "
                    . "e.`sigla` as 'sigla_estado', "
                    . "e.`populacao` as 'populacao', "
                    . "r.`nome` as 'nome_regiao' "
                    . "FROM estado e "
                    . "INNER JOIN regiao r ON r.id = e.id_regiao "
                    . "WHERE e.id = :id";
        } else if ($markerType === 2) {
            $query = "SELECT "
                    . "r.`nome` as 'nome_regiao', "
                    . "r.`populacao` as 'populacao' "
                    . "FROM regiao r "
                    . "WHERE r.id = :id";
        }

        $stmt = $this->controller->query($query);

        $stmt->bindInt("id", $id);

        return $stmt->fetchAssoc();
    }

    public function getCursoDetails($id) {
        $query = "SELECT 
                c.`id` as 'id_curso',
                CONCAT(mp.`avaliacao`,' (',mp.`ano_avaliacao`,')') as 'avaliacao',
                IF(man.`nome` IS NOT NULL, CONCAT(man.`nome`,' - ',man.`cnpj`), 'N/D') as 'mantenedora',
                clo.`nome` as 'local_de_oferta',
                IF(c.`total_de_alunos`<>0,c.`total_de_alunos`,'N/D') as 'total_de_alunos',
                IF(c.`carga_horaria`<>0,CONCAT(c.`carga_horaria`,' horas'),'N/D') as 'carga_horaria',
                c.`temp_faixa_enade` as 'nota',
                c.`matutino` as 'eh_matutino',
                c.`vespertino` as 'eh_vespertino',
                c.`noturno` as 'eh_noturno',
                c.`integral` as 'eh_integral',
                ri.`nome` as 'nome_do_curso',
                ri.`inicio_funcionamento` as 'inicio_do_funcionamento',
                m.`nome` as 'nome_do_municipio',
                m.`latitude` as 'latitude_municipio',
                m.`longitude` as 'longitude_municipio',
                m.`cod` as 'codigo_municipio',
                e.`nome` as 'nome_do_estado',
                e.`sigla` as 'sigla_do_estado',
                r.`nome` as 'nome_da_regiao',
                i.`id` as 'id_instituicao',
                i.`nome` as 'nome_da_instituicao',
                i.`sigla` as 'sigla_da_instituicao',
                c.adicionais as 'adicional',
                IFNULL(cg.`nome`,'N/D') as 'grau_academico',
                cm.`nome` as 'modalidade',
                cn.`nome` as 'nivel',
                IFNULL(o.`nome`,'N/D') as 'tipo_da_organizacao',
                IFNULL(cp.`nome`,'N/D') as 'nome_do_programa',
                IFNULL(cp.`cod`,'N/D') as 'codigo_do_programa',
                IFNULL(ad.`nome`,'N/D') as 'area_detalhada',
                IFNULL(ae.`nome`,'N/D') as 'area_especifica',
                IFNULL(ag.`nome`,'N/D') as 'area_geral',
                IFNULL(cr.`nome`,'N/D') as 'rede',
                IFNULL(cnpr.`nome`,'N/D') as 'natureza_privada',
                cnpu.`nome` as 'natureza_publica',
                c.`mapa` as 'mapa'
                FROM `curso` c
                LEFT JOIN `mapa` mp ON mp.id = c.mapa 
                LEFT JOIN `mantenedora` man ON man.id = c.id_mantenedora 
                LEFT JOIN `curso_local_oferta` clo ON clo.id = c.id_local_de_oferta 
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

    public function listCursosByMunicipio($cod_mun, $filters, $mapa) {
        $query = "SELECT "
                . "c.id as id "
                . ",ri.nome as nome "
                . ",i.sigla as instituicao "
                . ",i.nome as nome_instituicao "
                . ",c.id_modalidade as id_modalidade"
                . ",cn.nome as nivel "
                . "FROM curso c "
                . "INNER JOIN curso_nivel cn ON c.id_nivel = cn.id "
                . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                . "INNER JOIN estado e ON e.id = m.id_estado "
                . "INNER JOIN registro_inep ri ON ri.id = c.id_registro_inep "
                . "INNER JOIN instituicao i ON i.id = c.id_instituicao ";

        $stmt = $this->append_filter_to_query($filters, $query, false, " c.cod_municipio = :cod_mun AND c.mapa = :mapa ", false);
        $stmt->bindString("cod_mun", $cod_mun);
        $stmt->bindInt("mapa", $mapa);
        return $stmt->fetchAll();
    }

    public function listCursosByEstado($id_estado, $filters, $mapa) {
        $query = "SELECT "
                . "c.id as id "
                . ",ri.nome as nome "
                . ",i.sigla as instituicao "
                . ",i.nome as nome_instituicao "
                . ",c.id_modalidade as id_modalidade "
                . ",cn.nome as nivel "
                . "FROM curso c "
                . "INNER JOIN curso_nivel cn ON c.id_nivel = cn.id "
                . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                . "INNER JOIN estado e ON e.id = m.id_estado "
                . "INNER JOIN registro_inep ri ON ri.id = c.id_registro_inep "
                . "INNER JOIN instituicao i ON i.id = c.id_instituicao ";

        $stmt = $this->append_filter_to_query($filters, $query, false, " m.id_estado = :id_estado AND c.mapa = :mapa ", false);
        $stmt->bindString("id_estado", $id_estado);
        $stmt->bindInt("mapa", $mapa);
        return $stmt->fetchAll();
    }

    public function listCursosByRegiao($id_regiao, $filters, $mapa) {
        $query = "SELECT "
                . "c.id as id "
                . ",ri.nome as nome "
                . ",i.sigla as instituicao "
                . ",i.nome as nome_instituicao "
                . ",c.id_modalidade as id_modalidade "
                . ",cn.nome as nivel "
                . "FROM curso c "
                . "INNER JOIN curso_nivel cn ON c.id_nivel = cn.id "
                . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                . "INNER JOIN estado e ON e.id = m.id_estado "
                . "INNER JOIN registro_inep ri ON ri.id = c.id_registro_inep "
                . "INNER JOIN instituicao i ON i.id = c.id_instituicao ";

        $stmt = $this->append_filter_to_query($filters, $query, false, " e.id_regiao = :id_regiao AND c.mapa = :mapa ", false);
        $stmt->bindString("id_regiao", $id_regiao);
        $stmt->bindInt("mapa", $mapa);
        return $stmt->fetchAll();
    }

    public function listMarkersMunicipios($filters, $mapa) {

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

        $stmt = $this->append_filter_to_query($filters, $query, "c.cod_municipio", " c.mapa = :mapa ", " total DESC ");
        $stmt->bindInt("mapa", $mapa);
        return $stmt->fetchAll();
    }

    public function listMarkersEstado($filters, $mapa) {

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

        $stmt = $this->append_filter_to_query($filters, $query, "e.id", " c.mapa = :mapa ", " total DESC ");
        $stmt->bindInt("mapa", $mapa);
        return $stmt->fetchAll();
    }

    public function listMarkersRegiao($filters, $mapa) {

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

        $stmt = $this->append_filter_to_query($filters, $query, "e.id_regiao", " c.mapa = :mapa ", " total DESC ");
        $stmt->bindInt("mapa", $mapa);
        return $stmt->fetchAll();
    }

    function totais($table, $cod, $filters, $markerType, $mapa) {
        $query = "";
        $group_by = "";
        $order_by = "total DESC";
        
        if($table == "enade" && $mapa != 1){
            return array();
        }
        
        if ($table == "grau") {
            $query .= "SELECT cg.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_grau_academico cg ON cg.id = c.id_grau ";
            $group_by = " c.id_grau ";
        } else if ($table == "rede") {
            $query .= "SELECT cr.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_rede cr ON cr.id = c.id_rede ";
            $group_by = " c.id_rede ";
        } else if ($table == "modalidade") {
            $query .= "SELECT cm.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_modalidade cm ON cm.id = c.id_modalidade ";
            $group_by = " c.id_modalidade ";
        } else if ($table == "natureza") {
            $query .= "SELECT cn.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_natureza cn ON cn.id = c.id_natureza ";
            $group_by = " c.id_natureza ";
        } else if ($table == "naturezadep") {
            $query .= "SELECT cnd.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_natureza_departamento cnd ON cnd.id = c.id_natureza_departamento ";
            $group_by = " c.id_natureza_departamento ";
        } else if ($table == "nivel") {
            $query .= "SELECT cn.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_nivel cn ON cn.id = c.id_nivel ";
            $group_by = " c.id_nivel ";
        } else if ($table == "programa") {
            $query .= "SELECT cp.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN curso_programa cp ON cp.cod = c.id_programa ";
            $group_by = " c.id_programa ";
        } else if ($table == "tipoorganizacao") {
            $query .= "SELECT t.nome as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado "
                    . "INNER JOIN tipo_organizacao t ON t.id = c.id_tipo_organizacao ";
            $group_by = " c.id_tipo_organizacao ";
        } else if ($table == "enade") {
            $query .= "SELECT c.temp_faixa_enade as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado ";
            $group_by = " c.temp_faixa_enade ";
            $order_by = " c.temp_faixa_enade ASC ";
        } else if ($table == "estado") {
            $query .= "SELECT CONCAT(e.nome,' (',e.sigla,')') as nome, "
                    . "COUNT(c.id) as total "
                    . "FROM curso c "
                    . "INNER JOIN cidade m ON c.cod_municipio = m.cod "
                    . "INNER JOIN estado e ON e.id = m.id_estado ";
            $group_by = " e.id ";
        }

        if ($markerType == 0) {
            $stmt = $this->append_filter_to_query($filters, $query, $group_by, "c.cod_municipio = :cod_mun AND c.mapa = :mapa ", $order_by);
            $stmt->bindInt("cod_mun", $cod);
        } else if ($markerType == 1) {
            $stmt = $this->append_filter_to_query($filters, $query, $group_by, "m.id_estado = :id_estado AND c.mapa = :mapa ", $order_by);
            $stmt->bindInt("id_estado", $cod);
        } else if ($markerType == 2) {
            $stmt = $this->append_filter_to_query($filters, $query, $group_by, "e.id_regiao = :id_regiao AND c.mapa = :mapa ", $order_by);
            $stmt->bindInt("id_regiao", $cod);
        }
        $stmt->bindInt("mapa", $mapa);
        return $stmt->fetchAllAssoc();
    }

    private function append_filter_to_query($filters, $query, $group_by, $adictional, $order_by) {

        $first = true;
        $this->log = array();
        if (isset($filters->instituicao)) {
            $query .= $this->append_filter($filters->instituicao, "c.id_instituicao", "instituicao", $first);
        }
        if (isset($filters->grau)) {
            $query .= $this->append_filter($filters->grau, "c.id_grau", "grau", $first);
        }
        if (isset($filters->rede)) {
            $query .= $this->append_filter($filters->rede, "c.id_rede", "rede", $first);
        }
        if (isset($filters->modalidades)) {
            $query .= $this->append_filter($filters->modalidades, "c.id_modalidade", "modalidade", $first);
        }
        if (isset($filters->natureza)) {
            $query .= $this->append_filter($filters->natureza, "c.id_natureza", "natureza", $first);
        }
        if (isset($filters->naturezadep)) {
            $query .= $this->append_filter($filters->naturezadep, "c.id_natureza_departamento", "naturezadep", $first);
        }
        if (isset($filters->nivel)) {
            $query .= $this->append_filter($filters->nivel, "c.id_nivel", "nivel", $first);
        }
        if (isset($filters->programa)) {
            $query .= $this->append_filter($filters->programa, "c.id_programa", "programa", $first);
        }
        if (isset($filters->tipoorganizacao)) {
            $query .= $this->append_filter($filters->tipoorganizacao, "c.id_tipo_organizacao", "tipoorganizacao", $first);
        }
        if (isset($filters->regiao)) {
            $query .= $this->append_filter($filters->regiao, "e.id_regiao", "regiao", $first);
        }
        if (isset($filters->estado)) {
            $query .= $this->append_filter($filters->estado, "m.id_estado", "estado", $first);
        }
        if (isset($filters->enade)) {
            $query .= $this->append_filter($filters->enade, "c.temp_faixa_enade", "enade", $first);
        }
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
        if (isset($filters->grau)) {
            $this->bind_array_to_param($stmt, $filters->grau, "grau");
        }
        if (isset($filters->rede)) {
            $this->bind_array_to_param($stmt, $filters->rede, "rede");
        }
        if (isset($filters->modalidades)) {
            $this->bind_array_to_param($stmt, $filters->modalidades, "modalidade");
        }
        if (isset($filters->natureza)) {
            $this->bind_array_to_param($stmt, $filters->natureza, "natureza");
        }
        if (isset($filters->naturezadep)) {
            $this->bind_array_to_param($stmt, $filters->naturezadep, "naturezadep");
        }
        if (isset($filters->nivel)) {
            $this->bind_array_to_param($stmt, $filters->nivel, "nivel");
        }
        if (isset($filters->programa)) {
            $this->bind_array_to_param($stmt, $filters->programa, "programa");
        }
        if (isset($filters->tipoorganizacao)) {
            $this->bind_array_to_param($stmt, $filters->tipoorganizacao, "tipoorganizacao");
        }
        if (isset($filters->regiao)) {
            $this->bind_array_to_param($stmt, $filters->regiao, "regiao");
        }
        if (isset($filters->estado)) {
            $this->bind_array_to_param($stmt, $filters->estado, "estado");
        }
        if (isset($filters->enade)) {
            $this->bind_array_to_param($stmt, $filters->enade, "enade");
        }

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
