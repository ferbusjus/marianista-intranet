//////
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          /**/
          $this->load->view('constant');
          $this->load->view('view_header');
          $data['usuarios'] = $this->usuarios_model->ListarUsuarios();
          $this->load->view('usuarios/view_usuarios', $data);
          $this->load->view('view_footer');
          
	}
     public function deleteuser(){
        $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $Usuarios       = json_decode($this->input->post('MiUsuario'));
          $id             = base64_decode($Usuarios->Id);
          /*Array de response*/
           $response = array (
               "error_msg" => ""
         );
           $this->usuarios_model->EliminarUsuario($id);
           $response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>Usuario Eliminado Correctamente, La Información de Actualizara en 5 Segundos <meta http-equiv='refresh' content='5'></div>";
           echo json_encode($response);
     }
     public function nuevo(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          //$this->seguridad_model->SessionActivo($url);
          $data["titulo"] = "Nuevo Usuario";
          $this->load->view('constant');
          $this->load->view('view_header');
          $this->load->view('usuarios/view_nuevo_usuario',$data);
          $this->load->view('view_footer');
     }
     public function Editar($id){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $id                = base64_decode($id);
          $data["usuarios"]  = $this->usuarios_model->BuscarUsuario($id);
          $data["titulo"]    = "Editar Usuario";
          $this->load->view('constant');
          $this->load->view('view_header');
          $this->load->view('usuarios/view_nuevo_usuario',$data);
          $this->load->view('view_footer');
     }
     public function Save(){
          $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $this->seguridad_model->SessionActivo($url);
          $Usuarios           = json_decode($this->input->post('UsuariosPost'));
          $response = array (
                    "campo"     => "",
                    "error_msg" => ""
         );
          if($Usuarios->Nombre==""){
               $response["campo"]     = "Nombre";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El Nombre es Obligatorio</div>";
          }else if($Usuarios->Apellidos==""){
               $response["campo"]     = "Apellidos";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Campo Apellido es obligatorio</div>";
          }else if($Usuarios->Email==""){
               $response["campo"]       = "email";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Correo Es Obligatorio</div>";
          }else if($Usuarios->TipoUsuario=="0"){
               $response["campo"]       = "TipoUsuario";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>Tipo de Usuario es Obligatorio</div>";
          }else if($Usuarios->Password1==""){
               $response["campo"]       = "password1";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La Contraseña Es Obligatorio</div>";
          }else if($Usuarios->Password2==""){
               $response["campo"]       = "password2";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La confirmación de Contraseña Es Obligatorio</div>";
          }else if($Usuarios->Estatus=="0"){
               $response["campo"]       = "Estatus";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>El Estatus es Obligatorio</div>";
          }else if($Usuarios->Password1!=$Usuarios->Password2){
               $response["campo"]       = "password2";
               $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable><button type='button' class='close' data-dismiss='alert'>&times;</button>La confirmación de Contraseña Es Incorrecta</div>";
          }else{
               if($Usuarios->Id==""){
                    $ExisteEmail       = $this->usuarios_model->ExisteEmail($Usuarios->Email);
                    if($ExisteEmail==true){
                         $response["campo"]     = "email";
                         $response["error_msg"]   = "<div class='alert alert-danger text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button>El Email Ya esta en Uso</div>";
                    }else{

                         $RegistraUser    = array(
                         'NOMBRE'         => ucwords($Usuarios->Nombre),
                         'APELLIDOS'      => ucwords($Usuarios->Apellidos),
                         'EMAIL'          => $Usuarios->Email,
                         'FECHA_REGISTRO' => date('Y-m-j H:i:s'),
                         'ESTATUS'        => $Usuarios->Estatus,
                         'TIPO'           => $Usuarios->TipoUsuario,
                         'PASSWORD'       => crypt($Usuarios->Password1)
                         );    
                         $IdUsers = $this->usuarios_model->SaveUsuarios($RegistraUser);
                         //Asignamos Permisos Administrador
                         if($Usuarios->TipoUsuario=="1"){
                              for ($i = 2; $i <= 13; $i++) {
                                   $arrayPermisos = array(
                                      'Usuario'   => $IdUsers,
                                      'Proteccion'=> $i,
                                      'Estatus'   => 1
                                         );
                                   $this->usuarios_model->AsignaPermisosAdmin($arrayPermisos);
                              }
                         }
                         //Asignamos Permisos Vendedor
                         if($Usuarios->TipoUsuario=="1"){
                              for ($i = 8; $i <= 13; $i++) {
                                   $arrayPermisos = array(
                                      'Usuario'   => $IdUsers,
                                      'Proteccion'=> $i,
                                      'Estatus'   => 1
                                         );
                                   $this->usuarios_model->AsignaPermisosAdmin($arrayPermisos);
                              }
                         }
                         $response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button> Informacion Guardada Correctamente</div>";           
                    }
               }
               if($Usuarios->Id!=""){
                    $newPassword     = $Usuarios->Password1;
                    $newPassword     = strlen($newPassword);
                    if($newPassword >= 20){
                         $newPassword = $Usuarios->Password1;
                    }else{
                         $newPassword = crypt($Usuarios->Password1);
                    }
                    $UpdateUser      = array(
                    'NOMBRE'         => ucwords($Usuarios->Nombre),
                    'APELLIDOS'      => ucwords($Usuarios->Apellidos),
                    'EMAIL'          => $Usuarios->Email,
                    'ESTATUS'        => $Usuarios->Estatus,
                    'TIPO'           => $Usuarios->TipoUsuario,
                    'PASSWORD'       => $newPassword
                    );    
                    $this->usuarios_model->UpdateUsers($UpdateUser,$Usuarios->Id);
                    $response["error_msg"]   = "<div class='alert alert-success text-center' alert-dismissable> <button type='button' class='close' data-dismiss='alert'>&times;</button> Informacion Actualizada Correctamente</div>";
               }
          }
          echo json_encode($response);
     }
}