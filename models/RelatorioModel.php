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
    
    function commit(){
        $this->controller->commit();
    }
    
    function getLastInsertedId(){
        return $this->controller->getLastInsertedId();
    }
    
    function getLastquery() {
        return $this->lastquery;
    }

    public function listGrau() {
        return $this->controller->listTable("curso_grau_academico","nome");
    }

    public function listRede() {
        return $this->controller->listTable("curso_rede","nome");
    }

    public function listModalidade() {
        return $this->controller->listTable("curso_modalidade","nome");
    }

    public function listNatureza() {
        return $this->controller->listTable("curso_natureza","nome");
    }

    public function listNaturezaDepartamento() {
        return $this->controller->listTable("curso_natureza_departamento","nome");
    }

    public function listNivel() {
        return $this->controller->listTable("curso_nivel","nome");
    }

    public function listPrograma() {
        return $this->controller->listTable("curso_programa","nome");
    }

    public function listEstado() {
        return $this->controller->listTable("estado","nome");
    }

    public function listRegiao() {
        return $this->controller->listTable("regiao","nome");
    }

    public function listTipoOrganizacao() {
        return $this->controller->listTable("tipo_organizacao","nome");
    }
    
    public function listConfiguracoes() {
        $query = "SELECT id,nome,json,data_hora FROM relatorio";
        $stmt = $this->controller->query($query);
        return $stmt->fetchAll();
    }
    
    public function saveConfiguracoes($rotulo,$json) {
        
        $query = "INSERT INTO relatorio(nome,json,ip,data_hora) VALUES (:nome,:json,:ip,NOW())";
        
        $stmt = $this->controller->query($query);
        
        $stmt->bindString("nome", $rotulo);
        $stmt->bindString("json", $json);
        $stmt->bindCurrentIp("ip");
        
        return $stmt->execute();
    }

    public function getCursoDetails($id){
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
    
    public function getConfiguracoes($id){
        $query = "SELECT id,nome,json,data_hora FROM relatorio WHERE id = :id";
        $stmt = $this->controller->query($query);
        
        $stmt->bindInt("id", $id);
        
        return $stmt->fetchAssoc();
    }
    
    public function listCursos($cod_mun,$filters){
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
        
        $stmt = $this->append_filter_to_query($filters, $query, " c.cod_municipio = :cod_mun ");
        $stmt->bindString("cod_mun", $cod_mun);
        return $stmt->fetchAll();
    }
    
    public function listMarkers($filters) {

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
        
        $stmt = $this->append_filter_to_query($filters, $query, false);
        return $stmt->fetchAll();
    }

    
    private function append_filter_to_query($filters,$query,$adictional){
        
        $first = true;
        $this->log = array();
        
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
        
        if($adictional){
            if($first){
                $query .= "WHERE ";
            } else {
                $query .= "AND ";
            }
            $query .= $adictional." ";
        } else {
            $query .= "GROUP BY c.cod_municipio";
        }
        $stmt = $this->controller->query($query);
        
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
            $param = $this->filter_to_param($filter,$param);
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
            $work = str_replace("-", "_","".$value);
            $query .= ":".$param."_".$work;
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
                $work = str_replace("-", "_","".$value);
                $stmt->bindString($param."_".$work, $value);
                $this->log[] = "Definindo ".$param."_".$work." como ".$value;
            }
        }
    }

    function getLog() {
        return $this->log;
    }

}
