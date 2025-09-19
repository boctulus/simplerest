<?php

use Boctulus\Simplerest\Core\Libs\Config;

?>

<!-- Step: Thank you -->
<div class="step" data-step="12" data-step-alias="thank-you">
    <div class="step-content">
        <div class="success-message">
            <div class="success-icon">🎉</div>
            <h2>¡Solicitud enviada exitosamente!</h2>
            <p class="subtitle">Tu solicitud para habilitar documentos tributarios electrónicos ha sido recibida.</p>
            <div class="info-box">
                <h3>¿Qué sigue?</h3>
                
                <!-- Payment Information Card -->
                <div class="payment-card" style="background: #f8f9fa; border: 2px solid #007cba; border-radius: 8px; padding: 20px; margin: 20px 0;">
                    <h4 style="color: #007cba; margin-top: 0;">📋 Información para el pago</h4>
                    <div style="background: white; color:#141414; padding: 15px; border-radius: 5px; font-family: monospace;">
                        <strong><?= Config::get('typeform.payment_info.company_name', 'Company name') ?></strong><br>
                        RUT: <?= Config::get('typeform.payment_info.rut', 'RUT') ?><br>
                        <?= Config::get('typeform.payment_info.bank', 'Bank Info') ?><br>
                        <?= Config::get('typeform.payment_info.account_type', 'Account Type') ?><br>
                        Número: <?= Config::get('typeform.payment_info.account_number', 'Account Number') ?><br>
                        Email: <?= Config::get('typeform.payment_info.contact_email', 'Contact email') ?>
                    </div>
                </div>
                
                <ul>
                    <li>Realizar el pago con la información mostrada arriba</li>
                    <li>Recibirás un email de confirmación en las próximas horas</li>
                    <li>Nuestro equipo revisará tu solicitud dentro de 24-48 horas</li>
                    <li>Te contactaremos para confirmar la activación de tus documentos</li>
                </ul>
            </div>
            <div class="step-actions">
                <button type="button" class="btn-primary" onclick="DataPersistence.clearData(); window.location.reload()">
                    Nueva Solicitud
                </button>
            </div>
        </div>
    </div>
</div>