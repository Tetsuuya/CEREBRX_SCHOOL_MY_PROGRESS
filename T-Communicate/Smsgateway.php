<?php

    if (!defined('BASEPATH'))
        exit('No direct script access allowed');

    class Smsgateway {

        private $_CI;

        function __construct() {
            $this->_CI = & get_instance();
            $this->_CI->load->model('setting_model');
            $this->_CI->load->model('smsconfig_model');
        }

        private function logSMS($status, $gateway, $to, $msg, $details = '') {
            $log_dir = APPPATH . 'logs/';
            if (!is_dir($log_dir)) {
                @mkdir($log_dir, 0755, true);
            }
            $log_file = $log_dir . 'sms_debug.txt';
            
            // Fallback to website root directory if logs folder is not writeable
            if (!is_writable($log_dir) && is_writable(FCPATH)) {
                $log_file = FCPATH . 'sms_debug.txt';
            }
            
            $timestamp = date('Y-m-d H:i:s');
            $log_message = "[$timestamp] STATUS: $status | GATEWAY: $gateway | TO: $to | MSG: $msg";
            if (!empty($details)) {
                $log_message .= " | DETAILS: " . (is_string($details) ? $details : json_encode($details));
            }
            $log_message .= "\n";
            @file_put_contents($log_file, $log_message, FILE_APPEND);
        }

        function sentRegisterSMS($id, $send_to) {
            $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();
            $msg = $this->getStudentRegistrationContent($id);

            if (!empty($sms_detail)) {
                if ($sms_detail->type == 'clickatell') {
                    $params = array(
                        'apiToken' => $sms_detail->api_id
                    );
                    $this->_CI->load->library('clickatell', $params);
                    try {
                        $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                        $this->logSMS('SUCCESS', 'clickatell', $send_to, $msg, $result);
                        return true;
                    } catch (Exception $e) {
                        $this->logSMS('FAILED', 'clickatell', $send_to, $msg, $e->getMessage());
                        return true;
                    }
                } else if ($sms_detail->type == 'twilio') {
                    $params = array(
                        'mode' => 'sandbox',
                        'account_sid' => $sms_detail->api_id,
                        'auth_token' => $sms_detail->password,
                        'api_version' => '2010-04-01',
                        'number' => $sms_detail->contact,
                    );
                    $this->_CI->load->library('twilio', $params);
                    $from = $sms_detail->contact;
                    $to = $send_to;
                    $message = $msg;
                    $response = $this->_CI->twilio->sms($from, $to, $message);

                    if ($response->IsError) {
                        $this->logSMS('FAILED', 'twilio', $send_to, $msg, $response->ErrorMessage);
                        return true;
                    } else {
                        $this->logSMS('SUCCESS', 'twilio', $send_to, $msg, $response);
                        return true;
                    }
                } else if ($sms_detail->type == 'custom') {
                    $this->_CI->load->library('customsms');
                    $from = $sms_detail->contact;
                    $to = $send_to;
                    $message = $msg;
                    $response = $this->_CI->customsms->sendSMS($to, $message);
                    $this->logSMS('SENT (CUSTOM)', 'custom', $send_to, $msg, $response);
                } else {
                    $this->logSMS('FAILED (UNKNOWN TYPE)', $sms_detail->type, $send_to, $msg);
                }
            } else {
                $this->logSMS('FAILED (NO_ACTIVE_GATEWAY)', 'none', $send_to, $msg, 'The sms_config table is empty or has no active SMS gateway settings.');
            }
        }

        function sentAddFeeSMS($invoice_id, $sub_invoice_id, $send_to) {
            $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();
            $msg = $this->getAddFeeContent($invoice_id, $sub_invoice_id);
            if (!empty($sms_detail)) {
                if ($sms_detail->type == 'clickatell') {
                    $params = array(
                        'apiToken' => $sms_detail->api_id
                    );
                    $this->_CI->load->library('clickatell', $params);
                    try {
                        $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                        $this->logSMS('SUCCESS', 'clickatell', $send_to, $msg, $result);
                        return true;
                    } catch (Exception $e) {
                        $this->logSMS('FAILED', 'clickatell', $send_to, $msg, $e->getMessage());
                        return false;
                    }
                } else if ($sms_detail->type == 'twilio') {
                    $params = array(
                        'mode' => 'sandbox',
                        'account_sid' => $sms_detail->api_id,
                        'auth_token' => $sms_detail->password,
                        'api_version' => '2010-04-01',
                        'number' => $sms_detail->contact,
                    );
                    $this->_CI->load->library('twilio', $params);
                    $from = $sms_detail->contact;
                    $to = $send_to;
                    $message = $msg;
                    $response = $this->_CI->twilio->sms($from, $to, $message);

                    if ($response->IsError) {
                        $this->logSMS('FAILED', 'twilio', $send_to, $msg, $response->ErrorMessage);
                        return false;
                    } else {
                        $this->logSMS('SUCCESS', 'twilio', $send_to, $msg, $response);
                        return true;
                    }
                } else if ($sms_detail->type == 'custom') {
                    $this->_CI->load->library('customsms');
                    $from = $sms_detail->contact;
                    $to = $send_to;
                    $message = $msg;
                    $response = $this->_CI->customsms->sendSMS($to, $message);
                    $this->logSMS('SENT (CUSTOM)', 'custom', $send_to, $msg, $response);
                } else {
                    $this->logSMS('FAILED (UNKNOWN TYPE)', $sms_detail->type, $send_to, $msg);
                }
            } else {
                $this->logSMS('FAILED (NO_ACTIVE_GATEWAY)', 'none', $send_to, $msg, 'The sms_config table is empty or has no active SMS gateway settings.');
            }
        }

        public function getStudentRegistrationContent($id) {
            $session_name = $this->_CI->setting_model->getCurrentSessionName();
            $student = $this->_CI->student_model->get($id);
            $msg = "Dear " . $student['firstname'] . " " . $student['lastname'] .
                    ", your admission is confirm in Class: " . $student['class'] .
                    ", Section: " . $student['section'] . " for Session: " . $session_name . ", for more detail contact System Admin.";
            return $msg;
        }

        public function getAddFeeContent($invoice_id, $sub_invoice_id) {
            $fee = $this->_CI->studentfeemaster_model->getFeeByInvoice($invoice_id, $sub_invoice_id);
            $a = json_decode($fee->amount_detail);
            $record = $a->{$sub_invoice_id};
            $fee_amount = number_format((($record->amount + $record->amount_fine) - $record->amount_discount), 2, '.', ',');
            $msg = "Dear " . $fee->firstname . " " . $fee->lastname . ", Fees Amount Rs." . $fee_amount . "/-Received, for more detail contact System Admin.";
            return $msg;
        }

        public function sentNotificationSMS($message, $send_to) {
            $sms_detail = $this->_CI->smsconfig_model->getActiveSMS();
            if (!empty($sms_detail)) {
                if ($sms_detail->type == 'clickatell') {
                    $params = array(
                        'apiToken' => $sms_detail->api_id
                    );
                    $this->_CI->load->library('clickatell', $params);
                    try {
                        $result = $this->_CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $message]);
                        $this->logSMS('SUCCESS', 'clickatell', $send_to, $message, $result);
                        return true;
                    } catch (Exception $e) {
                        $this->logSMS('FAILED', 'clickatell', $send_to, $message, $e->getMessage());
                        return false;
                    }
                } else if ($sms_detail->type == 'twilio') {
                    $params = array(
                        'mode' => 'sandbox',
                        'account_sid' => $sms_detail->api_id,
                        'auth_token' => $sms_detail->password,
                        'api_version' => '2010-04-01',
                        'number' => $sms_detail->contact,
                    );
                    $this->_CI->load->library('twilio', $params);
                    $from = $sms_detail->contact;
                    $response = $this->_CI->twilio->sms($from, $send_to, $message);
                    if ($response->IsError) {
                        $this->logSMS('FAILED', 'twilio', $send_to, $message, $response->ErrorMessage);
                        return false;
                    } else {
                        $this->logSMS('SUCCESS', 'twilio', $send_to, $message, $response);
                        return true;
                    }
                } else if ($sms_detail->type == 'custom') {
                    $this->_CI->load->library('customsms');
                    $response = $this->_CI->customsms->sendSMS($send_to, $message);
                    $this->logSMS('SENT (CUSTOM)', 'custom', $send_to, $message, $response);
                    return true;
                } else {
                    $this->logSMS('FAILED (UNKNOWN TYPE)', $sms_detail->type, $send_to, $message);
                }
            } else {
                $this->logSMS('FAILED (NO_ACTIVE_GATEWAY)', 'none', $send_to, $message, 'The sms_config table is empty or has no active SMS gateway settings.');
            }
            return false;
        }

    }

    ?>