<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Members extends CI_Controller {

    var $template = 'template';

    function __construct() {
        parent::__construct();
        $this->load->model('Member');
//        for
    }

    function login() {

        $this->form_validation->set_rules('email', 'email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'password', 'required');
        $this->form_validation->set_error_delimiters('', '<br/>');

        if ($this->form_validation->run() == TRUE) {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $user = $this->User->checkLogin($email, $password);

            if (!empty($user)) {
                $sessionData['id'] = $user['id'];
                $sessionData['email'] = $user['email'];
                $sessionData['full_name'] = $user['full_name'];
                $sessionData['level'] = $user['level'];
                $sessionData['is_login'] = TRUE;

                $this->session->set_userdata($sessionData);
                $this->User->updateLastLogin($user['id']);

                if ($this->session->userdata('level') == 0) {
                    redirect('users/home');
                } else {
                    redirect('users');
                }
            }

            $this->session->set_flashdata('error', 'Login gagal!, email dan password Anda salah.');
            redirect('users/login');
        }

        $data['content'] = 'users/login';
        $this->load->view($this->template, $data);
    }

    function register() {
        $this->form_validation->set_rules('full_name', '<strong>Nama Lengkap</strong>', 'required');
        $this->form_validation->set_rules('email', '<strong>Email</strong>', 'required|valid_email');
        $this->form_validation->set_rules('password', '<strong>Password</strong>', 'required|min_length[3]|matches[confirm_password]');
        $this->form_validation->set_rules('confirm_password', '<strong>Konfirmasi Password</strong>', 'required');
        $this->form_validation->set_rules('phone', '<strong>Telepon/HP</strong>', 'trim|required|numeric');
        $this->form_validation->set_rules('address', '<strong>Alamat</strong>', 'required');
        $this->form_validation->set_rules('zip', '<strong>Kode Pos</strong>', 'trim|required|numeric');
        $this->form_validation->set_error_delimiters('', '<br/>');

        if ($this->form_validation->run() == TRUE) {
            $this->Member->create();
            $this->session->set_flashdata('success', 'Registrasi berhasil! Silakan login.');
            redirect('members/login');
        }
        $data['content'] = 'members/register';
        $this->load->view($this->template, $data);
    }

    function profile() {

        $this->form_validation->set_rules('full_name', 'full name', 'required');
        $this->form_validation->set_rules('email', 'email', 'required|valid_email');
        $this->form_validation->set_rules('phone', 'phone', 'required');
        $this->form_validation->set_rules('address', 'address', 'required');
        $this->form_validation->set_rules('zip', 'zip', 'required');
        if ($this->input->post('password')):
            $this->form_validation->set_rules('password', 'password', 'required|matches[confirm_password]');
            $this->form_validation->set_rules('confirm_password', 'confirm password', 'required');

        endif;
        $this->form_validation->set_error_delimiters('', '<br/>');

        if ($this->form_validation->run() == TRUE) {
            $this->User->update($this->session->userdata('id'));
            $this->session->set_flashdata('success', 'Profile disimpan');
            redirect('users/profile');
        }
        $data['user'] = $this->Member->getUserById($this->session->userdata('id'));
        $data['content'] = 'users/profile';
        $this->load->view($this->template, $data);
    }

    function forgot_password() {
        $this->form_validation->set_rules('email', 'email', 'required|valid_email|xss_clean');
        if ($this->form_validation->run() == TRUE) {
            $email = $this->input->post('email');
            $token = $this->general->generateRandomCode(50);
            $user = $this->User->findByEmail($email);
            if (!empty($user)) {
                $this->User->createToken($user['id'], $token);
                $userData = $this->User->getUserById($user['id']);

                $this->load->library('email');
                $this->email->from($this->general->getSetting('Admin.Email'), 'Admin ProjectTA');
                $this->email->to($email);

                $this->email->subject('Step Reset Password - ProjectTA');
                $message = '';
                $message .= 'You have sent request to reset password. Click link below and follow the step :<br/>';
                $message .= '<a href="' . base_url() . 'users/reset_password/?email=' . $userData['email'] . '&token=' . $userData['reset_token'] . '">' . base_url() . 'users/reset_password/?email=' . $userData['email'] . '&token=' . $userData['reset_token'] . '</a>';
                $this->email->message($message);
                $this->email->send();
                $this->session->set_flashdata('success', 'Reset password steps sent to your email. Check your inbox and follow the steps !');
                redirect('users/forgot_password');
            } else {
                $this->session->set_flashdata('error', 'Your email does not exist in our database ');
                redirect('users/login');
            }
        }
        $data['content'] = 'users/forgot_password';
        $this->load->view($this->template, $data);
    }

    function reset_password($email = null, $token = null) {
        $email = $this->input->get('email');
        $token = $this->input->get('token');


        $user = $this->User->findByEmailAndResetPasswordToken($email, $token);

        if (empty($user)) {
            $this->session->set_flashdata('error', 'Reset password token invalid !');
            redirect('users/login');
        } else {
            $data = array(
                'reset' => array(
                    'id' => $user['id'],
                    'token' => $token,
                    'email' => $email
                )
            );

            $this->session->set_userdata($data);

            $resetSession = $this->session->userdata('reset');
        }


        if (empty($resetSession)) {
            $this->session->set_flashdata('error', 'Reset password token invalid !');
            redirect('users/login');
        }

        $this->form_validation->set_rules('password', 'password', 'required|xss_clean');
        $this->form_validation->set_rules('confirm_password', 'confirm password', 'required|matches[password]|xss_clean');
        $this->form_validation->set_error_delimiters('','<br/>');
        if ($this->form_validation->run() == TRUE) {
            $this->User->resetPassword($resetSession['id'], $this->input->post('password'));
            $this->User->truncateToken($resetSession['id']);
            $this->session->set_flashdata('success', 'Reset password sucess, please login with your new password !');
            redirect('users/login');
        }

        $data['user'] = $user;
        $data['content'] = 'users/reset_password';
        $this->load->view($this->template, $data);
    }

    function home() {
        $this->general->cekUserLogin();
        $data['content'] = 'users/home';
        $this->load->view($this->template, $data);
    }

    function logout() {

        $this->session->sess_destroy();
        redirect('members/login');
    }

}
