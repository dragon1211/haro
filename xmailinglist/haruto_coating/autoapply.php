<?php

/**
 * Xserver Mailinglist & Mailmagazine Program 
 * http://www.xserver.ne.jp/
 * 
 * Copyright 2015 Xserver Inc.
 * http://www.xserver.co.jp/
 * 
 * Data: 2015-04-14T00:00:00+12:00
 */

//����ȥ���θƤӽФ�
require_once dirname(__FILE__) . '/admin/lib/CCtrlML.php';

//�᡼��󥰥ꥹ�����饹�Υ��󥹥�������
$mCtrlML = new CCtrlML();

$mCtrlML->AutoApplyMail();
?>
