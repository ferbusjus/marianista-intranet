<?php

/**
 * @package       modules/swp_egresos/controller
 * @name            swp_egresos.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2017 SISTEMAS-DEV
 * @version         1.0 - 24.09.2017
 */
class swp_egresos extends CI_Controller
{

    public $token = '';
    public $modulo = 'EGRESOS';
    public $datasession = '';

    public function __construct ()
    {
        parent::__construct ();
        $this->load->model ('alumno_model', 'objAlumno');
        $this->load->model ('egresos_model', 'objEgresos');
        $this->load->model ('seguridad_model');
        $this->datasession = $this->nativesession->get ('arrDataSesion');
    }

    public function index ()
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo ($url);
        /*if ($this->seguridad_model->restringirIp () == FALSE) {
            $this->load->view ('constant');
            $this->load->view ('view_header');
            $this->load->view ('view_default');
            $this->load->view ('view_footer');
        } else {*/
            $this->seguridad_model->registraNavegacion ($this->modulo);
            $data['razon'] = $this->objEgresos->getRazonSocial ();
            $data['responsable'] = $this->objEgresos->getResponsables ();
            $data['comprobantes'] = $this->objEgresos->getComprobantes ();
            $data['token'] = $this->token ();
            $this->load->view ('constant');
            $this->load->view ('view_header');
            $this->load->view ('egresos-js');
            $this->load->view ('lista-egresos-view', $data);
            $this->load->view ('view_footer');
       /* }*/
    }

    public function eliminaEgreso ()
    {
        $vId = $this->input->post ("vId");
        $resp = $this->objEgresos->eliminaEgreso ($vId);
        if ($resp) {
            $output = array('flg' => 0, 'msg' => 'SE ELIMINO CORRECTAMENTE EL EGRESO.');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL ELIMINAR EL EGRESO\n COMUNIQUESE CON EL ADMINISTRADOR.');
        }
        echo json_encode ($output);
    }

    public function filtroProveedor($tipo) {
        $returnArr = array();
        $txtFiltro = $this->input->get("term");
        $dataFiltro = $this->objEgresos->filtrarProveedor($txtFiltro, $tipo);
        foreach ($dataFiltro as $arrdata) {
            $rowArray['value']      = $arrdata->ruc . " | " . $arrdata->razon_social;
            $rowArray['ruc']         = $arrdata->ruc;
            $rowArray['razon']     = $arrdata->razon_social;
            array_push($returnArr, $rowArray);
        }
        echo json_encode($returnArr);
    }
    
    public function lista ()
    {
        if ($this->input->is_ajax_request ()) {
            $output = array();
            $arrData = array();           
            // $data = $this->objEgresos->listaEgresos ();
            $data = $this->objEgresos->get_datatables ();
            if (is_array ($data) && count ($data) > 0) {
                foreach ($data as $fila) {
                    $img = base_url () . '/images/bt_mini_delete.png';
                    $img2 = base_url () . '/images/bt_mini_edit.png';
                    $img_file = base_url () . '/images/downloads-icon.png';
                    $conf = "<img src='$img'  title='Eliminar' onclick=\"javascript:js_eliminar('$fila->id');\" style='cursor:pointer' />";
                    $conf .= "<img src='$img2'  title='Editar' onclick=\"javascript:js_editar('$fila->id');\" style='cursor:pointer' />";
                    $descargaFile1 = "<img src='$img_file'  title='Descargar Archivo 1' onclick=\"javascript:js_descargar('$fila->archivo_1');\" style='cursor:pointer' />";
                    $descargaFile2 = "<img src='$img_file'  title='Descargar Archivo 2' onclick=\"javascript:js_descargar('$fila->archivo_2');\" style='cursor:pointer' />";
                    $arrData [] = array(
                        "id" => $fila->id,
                        "usureg" => $fila->usureg,
                        "usumod" => $fila->usumod,
                        "razon" => $fila->razon,
                        "responsable" => $fila->responsable,
                        "comprobante" => $fila->comprobante,
                      //  "tipo_gasto" => ($fila->tipo_gasto=='1')?'BIENES': 'GASTOS',
                       // "inputacion" => $fila->inputacion,
                      //  "fecha" => $fila->fecha,
                        "archivo" => (($fila->archivo_1!='') ? $descargaFile1 : '')." ".(($fila->archivo_2!='') ? $descargaFile2 : ''),
                        "descripcion" => $fila->descripcion,
                       // "num_comprobante" => $fila->num_comprobante,
					    "fecha_pago" => $fila->fecha_pago,
						"estado" => ($fila->fecha_pago!="" && $fila->fecha_pago!="0000-00-00")? "<label style='color:blue'>PAGADO</label>" : "<label style='color:red'>PENDIENTE</label>",
                       // "proveedor" => $fila->proveedor,
                        "monto" => 'S/' .$fila->monto,
                        "fecreg" =>  $fila->fecreg,
                        "flgactivo" =>  $fila->flgactivo,
                        "fecmod" =>  $fila->fecmod,
                        "conf" => ($fila->flgactivo==1)?$conf:''
                    );
                }
                $output = array(
                    "draw" => intval ($_POST["draw"]),
                    "recordsTotal" => $this->objEgresos->count_all (),
                    "recordsFiltered" => $this->objEgresos->count_filtered (),
                    "data" => $arrData
                );
            } else {
                $output = array(
                    "draw" => intval ($_POST["draw"]),
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => $arrData
                );
            }
            echo json_encode ($output);
        } else {
            echo json_encode ($output);
        }
    }

    public function getGasto(){
         $output = array();
        if ($this->input->is_ajax_request ()) {
             $vId = $this->input->post ("vId");
             $data = $this->objEgresos->getDatosGasto($vId);
             $output = array('gasto' => $data);          
        } else {
            echo json_encode ($output);
        }
         echo json_encode ($output);
    }
    
    public function getdatos ()
    {
        $output = array();
        $arrDataResp= array();
        $arrDataRazon = array();
        $arrDataComprobante = array();
        $arrDataInputacion = array();
        $arrDataTipoGasto = array();
         $arrDataTipoCaja = array();
            
        $dataRazon = $this->objEgresos->getRazonSocial ();
        if (is_array ($dataRazon) && count ($dataRazon) > 0) {
            foreach ($dataRazon as $grupo) {
                $arrDataRazon [] = array(
                    'id' => $grupo->id_razon,
                    'value' => strtoupper ($grupo->razon_social)
                );
            }
        }
        
        $dataResp = $this->objEgresos->getResponsables ();
        if (is_array ($dataResp) && count ($dataResp) > 0) {
            foreach ($dataResp as $comp) {
                $arrDataResp [] = array(
                    'id' => $comp->id_responsable,
                    'value' => strtoupper ($comp->responsable)
                );
            }
        }    
        $dataComprobante = $this->objEgresos->getComprobantes ();
        if (is_array ($dataComprobante) && count ($dataComprobante) > 0) {
            foreach ($dataComprobante as $comp) {
                $arrDataComprobante [] = array(
                    'id' => $comp->idcomprobante,
                    'value' => strtoupper ($comp->descripcion)
                );
            }            
        }
        
        $dataInputacion = $this->objEgresos->getInputaciones ();
        if (is_array ($dataInputacion) && count ($dataInputacion) > 0) {
            foreach ($dataInputacion as $comp) {
                $arrDataInputacion [] = array(
                    'id' => $comp->id_inputacion,
                    'value' => strtoupper ($comp->descripcion)
                );
            }            
        }
        $dataTipoGasto[] = array('id' => 1, 'value' => 'BIENES');
        $dataTipoGasto[] = array('id' => 2, 'value' => 'SERVICIOS');
		$dataTipoGasto[] = array('id' => 3, 'value' => 'OTROS');
        foreach ($dataTipoGasto as $comp) {
            $arrDataTipoGasto [] = array(
                'id' => $comp['id'],
                'value' => strtoupper($comp['value'])
            );
        }
        
        $dataTipoCaja = $this->objEgresos->getCajas ();
        if (is_array ($dataTipoCaja) && count ($dataTipoCaja) > 0) {
            foreach ($dataTipoCaja as $comp) {
                $arrDataTipoCaja [] = array(
                    'id' => $comp->id_caja,
                    'value' => strtoupper ($comp->nomcaja)
                );
            }            
        }
        
        $output = array(
            'razon' => $arrDataRazon,
            'responsable' => $arrDataResp,
            'comprobante' => $arrDataComprobante,
            'inputacion' => $arrDataInputacion,
            'gasto' => $arrDataTipoGasto,
            'caja' => $arrDataTipoCaja
        );
        
        echo json_encode ($output);
    }

    public function grabarEgreso ()
    {    
        $output = array();
        $dataPost = array(
            'id_razon' => $this->input->post ("vrazon"),
            'id_responsable' => $this->input->post ("vresponsable"),
            'id_comprobante' => $this->input->post ("vtipocomp"),
            'id_tipo_gasto' => $this->input->post ("vtipogasto"), 
            'id_inputacion' => $this->input->post ("vimputacion"),
            'fecha' => $this->input->post ("vfecha"),
            'descripcion' => $this->input->post ("vdescripcion"),
            'num_comprobante' => $this->input->post ("vcomprobante"),
            'ruc_proveedor' => $this->input->post ("vruc"),
            'proveedor' => $this->input->post ("vproveedor"),
            'monto' => $this->input->post ("vmonto"),
            'usureg' => $this->datasession['USUCOD']
        );
        $resp = $this->objEgresos->grabaNuevoEgreso ($dataPost);
        if ($resp) {
            $output = array('flg' => 0, 'msg' => 'SE REGISTRO CORRECTAMENTE.');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL REGISTRAR\n COMUNIQUESE CON EL ADMINISTRADOR.');
        }
        echo json_encode ($output);
    }

    public function procesarGasto(){
        $targetDir = "gastos_files/";
        $accion = $_POST['accion'];
        unset($_POST['accion']);
        $files = $_FILES;
       /* echo "<pre>";
        print_r($files);
        echo "</pre>";*/
        $_POST['usureg'] = $this->datasession['USUCOD'];
        $input = $_POST;
        if($input['flg_ruc']=="0") { // Sin RUC
            $input['proveedor'] = strtoupper($input['proveedor']);
            // -- Verificamos si existe el RUC
            $con_prov = $this->objEgresos->consultarRuc(trim($input['ruc_proveedor']));
            if($con_prov == '0') {
                // -- Si no existe el Provedor lo registramos
                $data['ruc']                = $input['ruc_proveedor'];
                $data['razon_social'] = $input['proveedor'];
                $this->objEgresos->registrarRuc($data);
            }
        }
        // -- Registramos el Egreso
        $flgSave = (trim($input['id'])!="") ? 1 : 0;
		$input['flg_pagado'] = ($input['fecha_pago'] !="") ? 1 : 0;
        $resp = $this->objEgresos->grabaNuevoEgreso ($input, $flgSave, $input['id']);
        //echo "RESP : ".$resp; 
        if (is_int($resp)) {
            //-- Registramos los archivos
            //$images_arr = array();
            $i=1;
            foreach($_FILES['file']['name'] as $key=>$val){
                if($_FILES['file']['name'] != "") {
                    $image_name    = $_FILES['file']['name'][$key];
                    $tmp_name       = $_FILES['file']['tmp_name'][$key];
                    $size                 = $_FILES['file']['size'][$key];
                    $type                = $_FILES['file']['type'][$key];
                    $error              = $_FILES['file']['error'][$key];
                    // File upload path
                    $fileName = basename($_FILES['file']['name'][$key]);
                    //$nameFile = explode(".", $fileName);
                    //$fileType = pathinfo($targetDir . $fileName,PATHINFO_EXTENSION);
                    //$targetFilePath = $targetDir . $nameFile[0]."_".$this->token().".".$fileType;       
                    $targetFilePath = $targetDir . $fileName;
                     if(move_uploaded_file($_FILES['file']['tmp_name'][$key], $targetFilePath)){
                        //$images_arr[] = $targetFilePath;
                        $imagen = 'archivo_'.$i;
                        $data = array($imagen => $fileName);
                        $this->objEgresos->actualizarArchivos($resp, $data);
                    }
                    $i++;
                    //print_r($images_arr);                    
                }
            }    
            $output = array('flg' => 0, 'msg' => 'SE REGISTRO CORRECTAMENTE.','error'=>'');
        } else {
            $output = array('flg' => 1, 'msg' => 'OCURRIO UN ERROR AL REGISTRAR\n COMUNIQUESE CON EL ADMINISTRADOR.', 'error'=>$resp);
        }
        echo json_encode ($output);
        
    }
    public function token ()
    {
        $this->token = md5 (uniqid (rand (), true));
        $this->nativesession->set ('token', $this->token);
        return $this->token;
    }

}
