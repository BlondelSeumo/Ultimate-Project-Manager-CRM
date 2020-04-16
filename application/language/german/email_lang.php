<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['email_must_be_array'] = 'Die E-Mail-Validierungsmethode muss als Array übergeben werden.';
$lang['email_invalid_address'] = 'Ungültige E-Mail-Adresse: %s';
$lang['email_attachment_missing'] = 'Der folgende E-Mail-Anhang konnte nicht gefunden werden: %s';
$lang['email_attachment_unreadable'] = 'Dieser Anhang kann nicht geöffnet werden: %s';
$lang['email_no_from'] = 'Ohne "From"-Attribut kann keine E-Mail gesendet werden.';
$lang['email_no_recipients'] = 'Es wurde kein Empfänger angegeben. Verwenden Sie eine dieser Empfänger-Attribute: To, Cc, or Bcc';
$lang['email_send_failure_phpmail'] = 'Die E-Mail konnte nicht mit der PHP mail() Funktion gesendet werden. Ihr Server ist möglicherweise nicht für den Versand von E-Mails über die angegebene Methode konfiguriert.';
$lang['email_send_failure_sendmail'] = 'Die E-Mail konnte nicht über die PHP Sendmail Funktion gesendet werden. Ihr Server ist möglicherweise nicht für den Versand von E-Mails über die angegebene Methode konfiguriert.';
$lang['email_send_failure_smtp'] = 'Die E-Mail konnte nicht mit der PHP SMTP Funktion gesendet werden. Ihr Server ist möglicherweise nicht für den Versand von E-Mails über die angegebene Methode konfiguriert.';
$lang['email_sent'] = 'Ihre Nachricht wurde erfolgreich mit dem folgenden Protokoll gesendet: %s';
$lang['email_no_socket'] = 'Es konnte kein Socket für Sendmail geöffnet werden. Bitte überprüfen Sie die Einstellungen.';
$lang['email_no_hostname'] = 'Sie haben keinen SMTP-Hostnamen angegeben.';
$lang['email_smtp_error'] = 'Der folgende SMTP-Fehler ist aufgetreten: %s';
$lang['email_no_smtp_unpw'] = 'Fehler: Sie müssen einen SMTP-Benutzernamen und ein Kennwort einstellen.';
$lang['email_failed_smtp_login'] = 'Fehler beim Senden des AUTH LOGIN-Befehls. Fehlermeldung: %s';
$lang['email_smtp_auth_un'] = 'Der Benutzername konnte nicht authentifiziert werden. Fehlermeldung: %s';
$lang['email_smtp_auth_pw'] = 'Das Kennwort konnte nicht authentifiziert werden. Fehlermeldung: %s';
$lang['email_smtp_data_failure'] = 'Es konnten keine Daten gesendet werden: %s';
$lang['email_exit_status'] = 'Programm Abbruch-Code: %s';
