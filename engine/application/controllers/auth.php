<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Description of Home
 *
 * @author marwansaleh
 */
class Auth extends MY_Controller {
    function __construct() {
        parent::__construct();
        $this->data['body_class'] = 'login-img-body';
    }
    
    function index(){
        $this->load->helper('cookie');
        
        if ($this->users->isLoggedin()){
            if ($this->users->has_access('CAN_CP')){
                redirect('cms/dashboard');
            }else{
                redirect('home');
            }
        }
        
        if ($this->session->flashdata('message')){
            $this->data['message_error'] = create_alert_box($this->session->flashdata('message'), $this->session->flashdata('message_type'));
        }
        
        $cookie_login = $this->input->cookie('cookie-login');
        if ($cookie_login){
            $this->data['remember'] = json_decode($cookie_login);
        }else{
            $this->data['remember'] = NULL;
        }
        $this->data['submit'] = site_url('auth/login');
        $this->data['subview'] = 'login/index';
        //var_dump($cookie_login);
        $this->load->view('_layout_login', $this->data);
    }
    
    function login(){
        $this->load->helper('cookie');
        $this->load->library('form_validation');
        
        $rules = $this->user_m->rules_login;
        $this->form_validation->set_rules($rules);
        //exit(print_r($rules));
        if ($this->form_validation->run() != FALSE) {
            
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $remember = $this->input->post('remember');
        
            //check flag remember me to create cookie
            if ($remember){
                $cookie = array(
                    'name'   => 'cookie-login',
                    'value'  => json_encode(array('username'=>$username, 'password'=>$password)),
                    'expire' => strtotime('December 31 2020')
                );

                $this->input->set_cookie($cookie);
            }else{
                delete_cookie('cookie-login');
            }
            
            $user = $this->users->login($username, $password);
            
            if (!$user){
                $this->session->set_flashdata('message_type','error');
                $this->session->set_flashdata('message', $this->users->get_message());
            }
        }
        
        if (validation_errors()){
            $this->session->set_flashdata('message_type','error');
            $this->session->set_flashdata('message', validation_errors());
        }
        
        redirect('auth');
    }
    
    function logout(){
        
        $this->users->logout();
        redirect('auth');
    }
    
    
    function hashit(){
        $subject = $this->input->get('subject');
        
        echo $this->users->hash($subject);
    }
}

/*
 * file location: engine/application/controllers/auth.php
 */
