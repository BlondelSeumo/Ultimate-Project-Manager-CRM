<?php defined('BASEPATH') or exit('No direct script access allowed');
define('SMS_PURCHASE_CONFIRMATION', 'purchase_confirmation');
define('SMS_PURCHASE_PAYMENT_CONFIRMATION', 'purchase_payment_confirmation');
define('SMS_RETURN_STOCK', 'return_stock');
define('SMS_RETURN_STOCK_PAYMENT', 'return_stock_payment');
define('SMS_TRANSACTION_RECORD', 'transaction_record');
define('SMS_INVOICE_REMINDER', 'reminder_invoice');
define('SMS_INVOICE_OVERDUE', 'overdue_invoice');
define('SMS_PAYMENT_RECORDED', 'invoice_payment');
define('SMS_ESTIMATE_EXP_REMINDER', 'estimate_expiration');
define('SMS_PROPOSAL_EXP_REMINDER', 'proposal_expiration');
define('SMS_STAFF_REMINDER', 'staff_reminder');

class Sms
{
    private $triggers = [];

    private $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->set_default_triggers();
    }


    public function get_gateways()
    {
        $sms_gateway = [
            'twilio' => [
                'name' => 'Twilio',
                'id' => 'twilio',
                'info' => '<p>Twilio SMS integration is one way messaging, means that your customers won\'t be able to reply to the SMS. Phone numbers must be in format <a href="https://www.twilio.com/docs/glossary/what-e164" target="_blank">E.164</a>. Click <a href="https://support.twilio.com/hc/en-us/articles/223183008-Formatting-International-Phone-Numbers" target="_blank">here</a> to read more how phone numbers should be formatted.</p>',
                'options' => [
                    [
                        'name' => 'twilio_account_sid',
                        'label' => 'Account SID',
                        'value' => config_item('twilio_account_sid'),
                    ],
                    [
                        'name' => 'twilio_auth_token',
                        'label' => 'Auth Token',
                        'value' => config_item('twilio_auth_token'),
                    ],
                    [
                        'name' => 'twilio_phone_number',
                        'label' => 'Twilio Phone Number',
                        'value' => config_item('twilio_phone_number'),
                    ],
                ],
            ],
            'clickatell' => [
                'name' => 'Clickatell',
                'id' => 'clickatell',
                'info' => "<p>Clickatell SMS integration is one way messaging, means that your customers won't be able to reply to the SMS.</p>",
                'options' => [
                    [
                        'name' => 'clickatell_api_key',
                        'label' => 'API Key',
                        'value' => config_item('clickatell_api_key'),
                    ]
                ],
            ],
        ];

        return $sms_gateway;
    }

    public function get_available_triggers()
    {
        $triggers = $this->triggers;
        foreach ($triggers as $trigger_id => $triger) {
            $triggers[$trigger_id]['value'] = config_item('sms_template_' . $trigger_id);
            if (!empty($triger['sms_number'])) {
                if (!empty(config_item($trigger_id . '_sms_number'))) {
                    $sms_number = config_item($trigger_id . '_sms_number');
                } else {
                    $sms_number = get_admin_number();
                }
                $triggers[$trigger_id]['sms_number'] = $sms_number;
            }
        }
        return $triggers;
    }

    public function send($trigger, $phone, $merge_fields = [])
    {
        if ($phone == '') {
            return false;
        }
        $gateway = $this->get_activate_gateway();
        if ($gateway !== false) {
            $callable = $gateway['id'] . '_send_sms';
            if ($this->is_trigger_active($trigger) && function_exists($callable)) {
                $message = $this->parse_merge_fields($merge_fields, config_item('sms_template_' . $trigger));
                $retval = call_user_func_array($callable, [$phone, clear_textarea_breaks($message), $trigger]);
                return $retval;
            }
        }

        return false;
    }

    /**
     * Parse sms gateway merge fields
     * We will use the email templates merge fields function because they are the same
     * @param  array $merge_fields merge fields
     * @param  string $message the message to bind the merge fields
     * @return string
     */
    public function parse_merge_fields($merge_fields, $message)
    {
        $template = new stdClass();
        $template->message = $message;
        $template->subject = '';
        return _parse_template_merge_fields($template, $merge_fields)->message;
    }

    public function trigger_option_name($trigger)
    {
        return 'sms_template_' . $trigger;
    }

    public function is_any_trigger_active()
    {
        $triggers = $this->get_available_triggers();
        $active = false;
        foreach ($triggers as $trigger_id => $trigger_opts) {
            if ($this->_is_trigger_message_empty(config_item('sms_template_' . $trigger_id))) {
                $active = true;
                break;
            }
        }

        return $active;
    }

    private function _is_trigger_message_empty($message)
    {
        if (trim($message) === '') {
            return false;
        }
        return true;
    }

    public function is_trigger_active($trigger)
    {
        if ($trigger != '') {
            if (!$this->_is_trigger_message_empty(config_item('sms_template_' . $trigger))) {
                return false;
            }
        } else {
            return $this->is_any_trigger_active();
        }
        return true;
    }

    public function get_activate_gateway()
    {
        $active = false;
        foreach ($this->get_gateways() as $id => $gateway) {
            if (config_item($id . '_status') == '1') {
                $active = $gateway;
                break;
            }
        }
        return $active;
    }

    public function get_number($items)
    {
        echo config_item($items);
    }

    private function set_default_triggers()
    {

        $customer_merge_fields = [
            '{full_name}',
            '{client_name}',
            '{contact_email}',
        ];
        $supplier_merge_fields = [
            '{supplier_name}',
            '{supplier_email}',
        ];
        $purchase_merge_fields = [
            '{purchase_link}',
            '{purchase_ref}',
            '{purchase_date}',
            '{purchase_due_date}',
            '{purchase_status}',
            '{purchase_subtotal}',
            '{purchase_total}',
            '{site_name}',
        ];
        $return_merge_fields = [
            '{return_stock_link}',
            '{return_stock_ref}',
            '{return_stock_date}',
            '{return_stock_due_date}',
            '{return_stock_status}',
            '{return_stock_subtotal}',
            '{return_stock_total}',
            '{site_name}',
        ];
        $invoice_merge_fields = [
            '{invoice_link}',
            '{invoice_ref}',
            '{invoice_date}',
            '{invoice_due_date}',
            '{invoice_status}',
            '{invoice_subtotal}',
            '{invoice_total}',
            '{site_name}',
        ];
        $proposal_merge_fields = [
            '{proposal_ref}',
            '{proposal_link}',
            '{proposal_date}',
            '{proposal_due_date}',
            '{proposal_status}',
            '{proposal_subtotal}',
            '{proposal_total}',
            '{proposal_related_to}',
            '{site_name}',
        ];
        $triggers = [
            SMS_INVOICE_REMINDER => [
                'merge_fields' => array_merge($customer_merge_fields, $invoice_merge_fields),
                'label' => 'Invoice Reminder Notice' . ' ' . lang('template'),
                'info' => 'Send SMS when invoice reminder notice sent when send invoice to client primary contact.',
            ],
            SMS_INVOICE_OVERDUE => [
                'merge_fields' => array_merge($customer_merge_fields, $invoice_merge_fields),
                'label' => 'Invoice Overdue Notice' . ' ' . lang('template'),
                'info' => 'Send SMS when invoice overdue notice  sent to client primary contact.',
            ],
            SMS_PAYMENT_RECORDED => [
                'merge_fields' => array_merge($customer_merge_fields, $invoice_merge_fields, ['{payment_amount}', '{payment_date}']),
                'label' => 'Invoice Payment Recorded' . ' ' . lang('template'),
                'info' => 'Send SMS when invoice payment is saved.',
            ],
            SMS_ESTIMATE_EXP_REMINDER => [
                'merge_fields' => array_merge(
                    $customer_merge_fields,
                    [
                        '{estimate_link}',
                        '{estimate_ref}',
                        '{estimate_date}',
                        '{estimate_due_date}',
                        '{estimate_status}',
                        '{estimate_subtotal}',
                        '{estimate_total}',
                        '{site_name}',
                    ]
                ),
                'label' => 'Estimate Expiration Reminder' . ' ' . lang('template'),
                'info' => 'Send SMS when expiration Estimate  sent to client primary contact.',
            ],
            SMS_PROPOSAL_EXP_REMINDER => [
                'merge_fields' => $proposal_merge_fields,
                'label' => 'Proposal Expiration Reminder' . ' ' . lang('template'),
                'info' => 'Send SMS when expiration reminder send to Related Proposals.',
            ],
            SMS_PURCHASE_CONFIRMATION => [
                'merge_fields' => array_merge($supplier_merge_fields, $purchase_merge_fields),
                'label' => 'Purchase Notice' . ' ' . lang('template'),
                'info' => 'Send SMS when Purchase confirmation/update stock notice sent to ',
                'sms_number' => true,
            ],
            SMS_PURCHASE_PAYMENT_CONFIRMATION => [
                'merge_fields' => array_merge($supplier_merge_fields, $purchase_merge_fields, ['{payment_amount}', '{payment_date}']),
                'label' => 'Purchase payment Notice' . ' ' . lang('template'),
                'info' => 'Send SMS when Purchase payment confirmation notice sent.',
            ],
            SMS_RETURN_STOCK => [
                'merge_fields' => array_merge($customer_merge_fields,$supplier_merge_fields, $return_merge_fields),
                'label' => 'Purchase Return Stock Notice' . ' ' . lang('template'),
                'info' => 'Send SMS when Purchase return stock notice sent.',
            ],
            SMS_RETURN_STOCK_PAYMENT => [
                'merge_fields' => array_merge($customer_merge_fields,$supplier_merge_fields, $return_merge_fields, ['{payment_amount}', '{payment_date}']),
                'label' => 'Purchase Return Stock Payment Notice' . ' ' . lang('template'),
                'info' => 'Send SMS when Purchase return stock notice sent.',
            ],
            SMS_TRANSACTION_RECORD => [
                'merge_fields' => [
                    '{transaction_type}',
                    '{transaction_title}',
                    '{transaction_date}',
                    '{transaction_amount}',
                    '{transaction_account}',
                    '{transaction_balance}',
                    '{transaction_paid_by}',
                    '{transaction_link}',
                ],
                'label' => 'Transaction Record expense/deposit/transfer' . ' ' . lang('template'),
                'info' => 'Send SMS when Transaction Record expense/deposit/transfer notified for reminder.',
                'sms_number' => true,
            ],
            SMS_STAFF_REMINDER => [
                'merge_fields' => [
                    '{name}',
                    '{reference}',
                    '{reminder_description}',
                    '{reminder_date}',
                    '{reminder_related}',
                    '{reminder_related_link}',
                    '{site_name}',
                ],
                'label' => 'Staff Reminder' . ' ' . lang('template'),
                'info' => 'Send SMS when staff notified for reminder.',
            ],
        ];
        $this->triggers = $triggers;
    }
}
