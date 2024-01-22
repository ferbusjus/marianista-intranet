<?php

/**
 * @package       modules/sga_alumnos/controller
 * @name            sga_alumnos.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2017 SISTEMAS-DEV
 * @version         1.0 - 10.10.2017
 */
class sga_alumnos extends CI_Controller {

    public $token = '';
    public $modulo = 'ALUMNOS';

    public function __construct() {
        parent::__construct();
        $this->load->model('alumno_model', 'objAlumno');
        $this->load->model('seguridad_model');
        $this->load->model('salon_model', 'objSalon');
        $this->load->model('familia_model', 'objFamilia');
        $this->load->model('matricula_model', 'objMatricula');
    }

    public function index() {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo($url);
        /* if ($this->seguridad_model->restringirIp () == FALSE) {
          $this->load->view ('constant');
          $this->load->view ('view_header');
          $this->load->view ('view_default');
          $this->load->view ('view_footer');
          } else { */
        $this->seguridad_model->registraNavegacion($this->modulo);
        $data["dataNivel"] = $this->objSalon->getNivel();
        //$data["dataFamilia"] = $this->objFamilia->getFamilia ();
        //echo "<pre>"; print_r($data["dataFamilia"]); echo "</pre>"; exit;
        $data['vano'] = date("Y");
        $data['s_ano_vig'] = $this->nativesession->get('S_ANO_VIG');
        $data['dataAulas'] = $this->objSalon->getSalones();
        //$data['comprobantes'] = $this->objEgresos->getComprobantes ();
        $data['token'] = $this->token();
        $this->load->view('constant');
        $this->load->view('view_header');
        $this->load->view('alumnos-js');
        $this->load->view('lista-alumno-view', $data);
        $this->load->view('view_footer');
        /* } */
    }

    public function activar() {
        $vId = $this->input->post("vId");
        $vNemo = $this->input->post("vNemo");
        $resp = $this->objAlumno->activar($vId, $vNemo);
        if ($resp) {
            $output = array('flg' => 0, 'msg' => 'SE ACTIVO CORRECTAMENTE AL ALUMNO.', 'error' => '');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR EN EL PROCESO\n COMUNIQUESE CON EL ADMINISTRADOR.', 'error' => $resp);
        }
        echo json_encode($output);
    }

    public function eliminaAlumno() {
        $vId = $this->input->post("vId");
        $vNemo = $this->input->post("vNemo");
        $resp = $this->objAlumno->eliminaAlumno($vId, $vNemo);
        if ($resp) {
            $output = array('flg' => 0, 'msg' => 'SE BLOQUEO CORRECTAMENTE AL ALUMNO.', 'error' => '');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR EN EL PROCESO\n COMUNIQUESE CON EL ADMINISTRADOR.', 'error' => $resp);
        }
        echo json_encode($output);
    }

    public function saveUpdate() {
        $vtipo = $this->input->post("accion");
        if ($vtipo == 'insert') {
            $totalExiste = $this->objAlumno->existeAlumno($this->input->post("txtdni"));
            if ((int) $totalExiste > 0) {
                $output = array('flg' => 1, 'msg' => 'EL ALUMNO CON EL DNI INGRESADO YA EXISTE.', 'error' => '');
                echo json_encode($output);
                exit;
            }
        }
        /* echo "<pre>";
          print_r ($_POST);
          echo "<pre>";
          exit; */
        $arrdata = array(
            'ALUCOD' => $this->input->post("txtcodigo"),
            'DNI' => $this->input->post("txtdni"),
            'APEPAT' => $this->input->post("txtpaterno"),
            'APEMAT' => $this->input->post("txtmaterno"),
            'NOMBRES' => $this->input->post("txtnombre"),
            'NOMCOMP' => ($this->input->post("txtpaterno") . " " . $this->input->post("txtmaterno") . ", " . $this->input->post("txtnombre")),
            'DIRECCION' => $this->input->post("txtdireccion"),
            'FAMCOD' => $this->input->post("cb_familia"),
            'PROCEDE' => $this->input->post("txtprocede"),
            'TELEFONO' => $this->input->post("txttelefono"),
            'TELEFONO2' => $this->input->post("txttelefono2"),
            'NUMLIBRO' => $this->input->post("txtlibro")
        );
        $txt = "";
        $flag = 0;
        if ($vtipo == 'insert') {
            $vchk = $this->input->post("hcombo");
            $vAluGen = $this->objAlumno->generadorCodigo('ALUMNO');
            $data = array(
                'FAMDES' => $this->input->post("txtfamilia"),
                'PADAPEPAT' => $this->input->post("txtpaterno"),
                'MADAPEPAT' => $this->input->post("txtmaterno")
            );
            $arrdata['ALUCOD'] = $vAluGen;
            $arrdata['ESTADO'] = 'N';
            $arrdata['MATRICULA'] = 'N';
            $arrdata['DISCOD'] = 'D';
            if ($vchk == '0') {
                $arrdata['FAMCOD'] = $this->objFamilia->insertSimple($data); // REGISTRAR A LA NUEVA FAMILIA
            } else {
                $vfamcod = $this->input->post("cb_familia");
                $datos = array('FLAG' => '1');
                $this->objFamilia->updateEstadoFamilia($vfamcod, $datos);
            }
            $txt = "REGISTRO";
            $flag = 1;
        } else {
            $famCod = $this->input->post("hcb_familia");
            $arrdata['FAMCOD'] = $famCod;
            $data = array(
                'FAMDES' => $this->input->post("txtpaterno") . " " . $this->input->post("txtmaterno"),
                'PADAPEPAT' => $this->input->post("txtpaterno"),
                'MADAPEPAT' => $this->input->post("txtmaterno"),
                'FLAG' => '1'
            );
            $this->objFamilia->update($data, $famCod);
            $txt = "MODIFICO";
        }
        /*
          echo "<pre>";
          print_r ($arrdata);
          echo "</pre>";
          exit;
         */
        $resp = $this->objAlumno->saveUpdate($arrdata, $flag);
        if ($resp) {
            if ($txt == 'REGISTRO') {
                $output = array('flg' => 0, 'msg' => 'SE ' . $txt . ' CORRECTAMENTE LOS DATOS DEL ALUMNO\nPROCEDER CON LA MATRICULA.', 'error' => '');
            } else {
                $output = array('flg' => 0, 'msg' => 'SE ' . $txt . ' CORRECTAMENTE LOS DATOS DEL ALUMNO.', 'error' => '');
            }
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR EN EL PROCESO\n COMUNIQUESE CON EL ADMINISTRADOR.', 'error' => $resp);
        }
        echo json_encode($output);
    }

    public function validaFamilia() {
        $datosjson = array();
        $vpat = $this->input->post("vpat");
        $vmat = $this->input->post("vmat");
        $existe = $this->objAlumno->validaFamiliaExistente($vpat, $vmat);
        if ($existe > 0) {
            $datosjson = array('flgtotal' => $existe, 'msg' => 'EXISTE LA FAMILIA : ' . $vpat . ' ' . $vmat . '*MARQUE LA OPCION "TIENE HERMANOS" Y*LUEGO SELECCIONE LA FAMILIA.');
        } else {
            $datosjson = array('flgtotal' => 0, 'msg' => '');
        }
        echo json_encode($datosjson);
    }

    public function getDatosAlumno() {
        $arrAlumno = array();
        $vId = $this->input->post("vId");
        $dataAlumno = $this->objAlumno->getDataAlumno($vId);
        if (count($dataAlumno) > 0) {
            $arrAlumno = $dataAlumno;
        }
        echo json_encode($arrAlumno);
    }

    public function getCursoCargo() {
        $arrAlumno = array();
        $json = array();
        $vId = $this->input->post("vId");
        $dataAlumno = $this->objAlumno->getCursoCargo($vId);
        //echo "<pre>"; print_r($dataAlumno); echo "</pre>"; exit;
        if (count($dataAlumno) > 0) {
            $dataHead = array(
                'codigo' => $dataAlumno->codigo,
                'nomcomp' => $dataAlumno->nomcomp
            );
            $dataCurso = array();
            if ($dataAlumno->tipo == 'I') {
                $vlimite = 8;
                $dataCurso = array(
                    0 => 'Matemáticas',
                    1 => 'Comunicacián',
                    2 => 'Personal Social',
                    3 => 'Ciencia y Ambiente',
                    4 => 'Religion',
                    5 => 'Psicomotrocidad',
                    6 => 'Computacion',
                    7 => 'Ingles'
                );
            } elseif ($dataAlumno->tipo == 'P') {
                $vlimite = 9;
                $dataCurso = array(
                    0 => 'Arte',
                    1 => 'Ciencia y Ambiente',
                    2 => 'Comunicación',
                    3 => 'Edu. Física',
                    4 => 'Religión',
                    5 => 'Matemáticas',
                    6 => 'Pers. Social',
                    7 => 'Ingles',
                    8 => 'Computación'
                    //9 => 'Tutoria'
                );
            } elseif ($dataAlumno->tipo == 'S') {
                $vlimite = 10;
                $dataCurso = array(
                    0 => 'Arte',
                    1 => 'Ciencia y Tec',
                    2 => 'Ciencias Sociales',
                    3 => 'Comunicacion',
                    4 => 'DPCC',
                    5 => 'Edu. Fisica',
                    6 => 'Computacion',
                    7 => 'Religión',
                    8 => 'Ingles',
                    9 => 'Matematicas'
                );
            }
            $dataNotas = array();
            for ($x = 1; $x <= $vlimite; $x++) {
                $vcampo = "c" . $x . "_pb";
                if ((int) $dataAlumno->$vcampo <= 10) {
                    $dataNotas[] = strtoupper($dataCurso[($x - 1)]) . "-" . $dataAlumno->$vcampo;
                }
            }
            // echo "<pre>"; print_r($dataNotas); echo "</pre>"; exit;
            $json = array(
                'arrHead' => $dataHead,
                'arrBody' => $dataNotas
            );
        }
        echo json_encode($json);
    }

    public function getDatosDocumentos() {
        $vId = $this->input->post("vId");
        $arrDataDocumentos = array();
        $dataDocu = $this->objAlumno->getDatosDocumentos($vId);
        foreach ($dataDocu as $docu) {
            $arrDataDocumentos[] = $docu;
        }
        echo json_encode($arrDataDocumentos);
    }

    public function getGrado($vNivel = '') {
        $arrDataGrado = array();
        $dataGrado = $this->objAlumno->getListaGrado($vNivel);
        foreach ($dataGrado as $grado) {
            $arrDataGrado[$grado->GRADOCOD] = $grado->GRADODES;
        }
        echo json_encode($arrDataGrado);
    }

    public function getFamilias() {
        $arrDataFamilia = array();
        $dataFamilia = $this->objFamilia->getFamilia();
        foreach ($dataFamilia as $familia) {
            $arrDataFamilia[$familia->FAMCOD] = $familia->FAMDES;
        }
        echo json_encode($arrDataFamilia);
    }

    public function getSeccion($vNivel = '', $vGrado = '', $flg = 0) {
        $arrDataSecc = array();
        $dataSeccion = $this->objAlumno->getListaAulas($vNivel, $vGrado, $flg);
        foreach ($dataSeccion as $lstSecc) {
            $arrDataSecc[$lstSecc->AULACOD] = $lstSecc->AULADES;
        }
        echo json_encode($arrDataSecc);
    }

    public function getSeccionMatricula($vNivel = '', $vGrado = '', $flg = 0) {
        $arrDataSecc = array();
        $dataSeccion = $this->objAlumno->getListaAulas($vNivel, $vGrado, $flg);
        foreach ($dataSeccion as $lstSecc) {
            $arrDataSecc[$lstSecc->AULACOD] = $lstSecc->NEMO."*".$lstSecc->AULADES."*".$lstSecc->total."*".$lstSecc->LIMITE;
        }
        echo json_encode($arrDataSecc);
    }
	
    public function lista() {
        if ($this->input->is_ajax_request()) {
            $output = array();
            $arrData = array();
            $data = $this->objAlumno->get_datatables();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $fila) {
                    $conf = "";
                    $img = base_url() . '/images/bt_mini_delete.png';
                    // $conf = "<img src='$img'  title='Eliminar' onclick=\"javascript:js_eliminar('$fila->ALUCOD');\" style='cursor:pointer' />";                    
                    if ($fila->ESTADO == 'V') {
                        $conf .= "<span style='font-size:15px; color:blue;cursor:pointer;' onclick=\"javascript:js_editar('$fila->ALUCOD');\"  class='glyphicon glyphicon-pencil' data-toggle='tooltip' title='Editar'></span>";
                        $conf .= "&nbsp;<span style='font-size:15px; color:red;cursor:pointer;' onclick=\"js_boqueo('$fila->ALUCOD','$fila->NEMO');\" class='glyphicon glyphicon-ban-circle'  data-toggle='tooltip' title='Bloquear'></span>";
                    } else {
                        $conf .= "&nbsp;<span style='font-size:15px; color:black;cursor:pointer;' onclick=\"js_activar('$fila->ALUCOD','$fila->NEMO');\" class='glyphicon glyphicon-ok-circle'  data-toggle='tooltip' title='Activar'></span>";
                    }
                    //$conf .="&nbsp;<span style='font-size:15px; color:black;cursor:pointer;'  class='glyphicon glyphicon-share-alt'  data-toggle='tooltip' title='Trasladar'></span>";
                    //if ($fila->ESTADO == 'V')
                    // $conf .="&nbsp;<span style='font-size:15px; color:black;cursor:pointer;' onclick=\"js_matricular('$fila->ALUCOD','$fila->FLG_MATRICULA');\" class='glyphicon glyphicon-tasks'  data-toggle='tooltip' title='Matricular'></span>";

                    /* $conf ='<div class="btn-group">
                      <button class="btn btn-warning btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                      Acciones<span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                      <li>
                      <a href="javascript:void(0)" id="acEditar">
                      <span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;
                      <span >Editar</span>
                      </a>
                      </li>
                      <li>
                      <a href="javascript:void(0)" class="acEliminar">
                      <span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;
                      <span>Eliminar</span>
                      </a>
                      </li>
                      </ul>
                      </div> '; */
                    $vImgMatricula = "PENDIENTE";
                    if ($fila->FLG_MATRICULA == 1) {
                        $vImg = base_url() . '/img/pagado.png';
                        $vImgMatricula = "<img src='$vImg' width='26px' height='26px'  title='Matriculado' style='cursor:pointer' />";
                    }


                    $arrData [] = array(
                        "codigo" => $fila->ALUCOD,
                        "nomcomp" => $fila->NOMCOMP,
                        "aula" => $fila->AULADES,
                        "nivel" => $fila->INSTRUCOD,
                        "grado" => $fila->GRADOCOD,
                        "matricula" => $vImgMatricula,
                        "estado" => (($fila->ESTADO == 'R') ? '<b>RETIRADO</b>' : 'VIGENTE'),
                        "conf" => $conf
                    );
                }
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => $this->objAlumno->count_all(),
                    "recordsFiltered" => $this->objAlumno->count_filtered(),
                    "data" => $arrData
                );
            } else {
                $output = array(
                    "draw" => intval($_POST["draw"]),
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => $arrData
                );
            }
            echo json_encode($output);
        } else {
            echo json_encode($output);
        }
    }

    public function token() {
        $this->token = md5(uniqid(rand(), true));
        $this->nativesession->set('token', $this->token);
        return $this->token;
    }

}
