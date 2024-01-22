<?php

/**
 * @package       modules/sga_marcacion/controller
 * @name            sga_marcacion.php
 * @category      Controller
 * @author         Fernando Bustamante Justiniano <ffernandox@hotmail.com.pe>
 * @copyright     2016 SISTEMAS-DEV
 * @version         1.0 - 2016/02/07
 */
class sga_marcacion extends CI_Controller
{

    public function __construct ()
    {
        parent::__construct ();
        $this->load->model ('asistencia_model', 'objAsistencia');
        $this->load->model ('seguridad_model');
    }

    public function index ()
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->seguridad_model->SessionActivo ($url);

        $this->load->view ('constant');
        $this->load->view ('view_header_free');
        $this->load->view ('view_marcacion');
        $this->load->view ('view_footer');
    }

    public function ajax ()
    {
        $cadHTML = '';
        $vCodigo = $this->input->post ("vCodigo");
        $vHora = $this->input->post ("vHora");
        $vHlibre = $this->input->post ("vHora");
        $vchk = $this->input->post ("vchk");

        $data = $this->objAsistencia->get_alumno ($vCodigo);       
        //print_r($data);
        if ($data) {
             $vCodigo = $data[0]->id_alumno;
            $tipo = $data[0]->t_tipo; // tipo de usuario P:PROFESOR / A:ALUMNO
            $vnomcomp = $data[0]->nomcomp;
            $valucod = $data[0]->alucod;
            if($tipo=='P') {
                $nivel = 'D';
            } else {
                $nivel = $data[0]->instrucod;
            }
            $dataRango = $this->objAsistencia->get_rango_ingreso ($nivel);
            $HIP = $dataRango[0]->ingreso;
            $HSP = $dataRango[0]->salida;
            $HTOL = $dataRango[0]->tolerancia;
            $VPHIP = explode (':', $HIP);
            $VvHora = explode (':', $vHora);

            # Se agrega este bloque para poner puntual / Tardanza a los alumnos
            if ($vchk == 'P') {
                $TA = 'P';
                $vHora = $HIP . ":00";
            } elseif ($vchk == 'T') {
                $TA = 'T';
                $vHora = "08:01:00";
            } else {
                if (((int) $VvHora[0] <= (int) $VPHIP[0]) && ((int) $VvHora[1] <= (int) ($VPHIP[1] + $HTOL)))
                    $TA = 'P';
                else
                    $TA = 'T';
            }
            # ==================================================================
            $verMarca = $this->objAsistencia->verificaAsistencia ($vCodigo, 1);
            $vHoraMarca = trim ($VvHora[0] . ':' . $VvHora[1]);
            $vtptrans = $this->calcularHoras ($HIP, $vHoraMarca);

            if (count ($verMarca) > 0 && $vtptrans <= 60) {
                $cadHTML = ' <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                          <td align="center" style="font-weight:bold; font-size:20px; color:#900"> UD. YA MARCO SU ASISTENCIA </td>
                                    </tr>
                                  </table>';
            } else {
                $arrAsi = $this->objAsistencia->verificaAsistencia ($vCodigo);                
                if ($arrAsi == TRUE && count ($arrAsi) > 0) {
                    if ($arrAsi[0]->evento == 'I') {
                        $VRMin = (int) ($VPHIP[1]);
                        $VRHor = (int) ($VPHIP[0]);
                        $arrAsi = array();
                        $arrAsi[0] = $vCodigo;
                        $arrAsi[1] = $vHora;
                        $arrAsi[2] = $TA;
                        $arrAsi[3] = '1';
                        $this->objAsistencia->actualizaMarca ($arrAsi);
                        // -------- Creando registro para la Salida --------->
                        $arrAsi = array();
                        $arrAsi[0] = null;
                        $arrAsi[1] = $vCodigo;
                        $arrAsi[2] = $vnomcomp;
                        $arrAsi[3] = $nivel;
                        $arrAsi[4] = date ('Y-m-d');
                        // Debe de modificarse para PRIMARIA / INICIAL 
                        $arrAsi[5] = '00:00:00';
                        $arrAsi[6] = 'S';
                        $arrAsi[7] = '';     
                        if($tipo=='P') {
                            $arrAsi[8] = 'P'; // tipo profe 
                        } else {
                            $arrAsi[8] = 'A'; // tipo alumno 
                        }
                        $arrAsi[9] = '';
                        $arrAsi[10] = '0';
                        $this->objAsistencia->insertaAsistencia ($arrAsi);

                        $cadHTML = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                              <td align="center" style="font-weight:bold; font-size:22px; color:red">BIENVENIDO(A)</td>
                                        </tr>
                                        <tr> 
                                              <td align="center"  style="font-weight:bold; font-size:20px">' . $vnomcomp . '</td>
                                        </tr>
                                      </table>';
                    } elseif ($arrAsi[0]->evento == 'S') {
                        $vHora = $vHlibre;
                        $TA = ($vHora <= $HSP) ? 'A' : 'D';
                        $this->objAsistencia->updateAsis ($TA, $vHora, $vCodigo);
                        $cadHTML = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                              <td align="center" style="font-weight:bold; font-size:22px; color:#900">
                                                HASTA LUEGO 
                                            </td>
                                        </tr>
                                        <tr>
                                              <td align="center"  style="font-weight:bold; font-size:20px">' . $vnomcomp . '</td>
                                        </tr>
                                      </table>';
                    }
                } else {
                    $cadHTML = ' <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                          <td align="center" style="font-weight:bold; font-size:20px; color:#900"> EL SISTEMA NO GENERO SU ASISTENCIA </td>
                                    </tr>
                                  </table>';
                }
            }
            echo $cadHTML;
        } else {
            echo 'Err';
        }
    }

    public function calcularHoras ($hora1, $hora2)
    {
        $separar[1] = explode (':', $hora1);
        $separar[2] = explode (':', $hora2);
        $total_minutos_trasncurridos[1] = ($separar[1][0] * 60) + $separar[1][1];
        $total_minutos_trasncurridos[2] = ($separar[2][0] * 60) + $separar[2][1];
        $total_minutos_trasncurridos = (int) $total_minutos_trasncurridos[1] - (int) $total_minutos_trasncurridos[2];
        if ($total_minutos_trasncurridos <= 59)
            return(abs ($total_minutos_trasncurridos));
        elseif ($total_minutos_trasncurridos > 59) {
            $HORA_TRANSCURRIDA = round ($total_minutos_trasncurridos / 60);
            if ($HORA_TRANSCURRIDA <= 9)
                $HORA_TRANSCURRIDA = '0' . $HORA_TRANSCURRIDA;
            $MINUITOS_TRANSCURRIDOS = $total_minutos_trasncurridos % 60;
            if ($MINUITOS_TRANSCURRIDOS <= 9)
                $MINUITOS_TRANSCURRIDOS = '0' . $MINUITOS_TRANSCURRIDOS;
            return ($HORA_TRANSCURRIDA . ':' . $MINUITOS_TRANSCURRIDOS);
        }
    }

}
