<style>
    .invoice-box {
        max-width: 900px; 
    }
</style>

<div class="invoice-box">
    <div class="text-center page-header">
        <h2 class="fw-bolder">ADOON COMPUTER PROGRAMMING SERVICES</h2>
            <p style="margin-top: 20px; font-weight: 600;">PUROK 7, POB. 1, SANTA TERESITA, BATANGAS</p>
            <p style="font-weight: 600;"><strong>REVELYN R. PADUA</strong> - Prop.</p>
            <p style="font-weight: 600;">NON-VAT Reg. TIN 322-610-092-000</p>
            
            <h3 class="fw-bolder" style="margin-top: 20px;">SALES INVOICE</h3>
    </div>

     <!-- Cabecera alineada -->
     <div class="d-flex justify-content-between secondary-header mt-4">
        <div>
            <p><strong>Sold to:</strong> <?= htmlspecialchars($header['sold_to'] ?? '') ?> </p>
            <p><strong>Business Style:</strong> <?= htmlspecialchars($header['business_style'] ?? '') ?></p>
            <p><strong>TIN/SC-TIN:</strong> <?= htmlspecialchars($header['tin'] ?? '') ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($header['address'] ?? '') ?></p>
        </div>
        <div class="text-end">
            <p><strong>Date:</strong> <?= htmlspecialchars($header['date'] ?? '') ?></p>
            <p><strong>Terms:</strong> <?= htmlspecialchars($header['terms'] ?? '_______') ?></p>
            <p><strong>OSCA/PWD ID No:</strong> <?= htmlspecialchars($header['osca_pwd_id'] ?? '_______') ?></p>
        </div>
    </div>

    <div class="invoice-container">
            <!-- Tabla de artículos -->
            <table class="table table-bordered" class="articles">
                <thead class="table-light text-center">
                    <tr>
                        <th width="10%">QTY</th>
                        <th width="15%">UNIT</th>
                        <th width="45%">ARTICLES</th>
                        <th width="15%">UNIT PRICE (USD)</th>
                        <th width="15%">AMOUNT (USD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Add rows
                    foreach ($rows as $row) {
                        echo '
                        <tr>
                            <td class="qty-col">' . htmlspecialchars($row['qty'] ?? '') . '</td>
                            <td class="unit-col">' . htmlspecialchars($row['unit'] ?? '') . '</td>
                            <td class="articles-col">' . htmlspecialchars($row['articles'] ?? '') . '</td>
                            <td class="price-col">' . number_format($row['unit_price'] ?? 0, 2) . '</td>
                            <td class="amount-col">' . number_format($row['amount'] ?? 0, 2) . '</td>
                        </tr>';
                    }

                    // Add empty rows
                    for ($i = count($rows); $i < 5; $i++) {
                        echo '
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>';
                    }
                    ?>
                    
                    <!-- Totales con líneas -->
                    <table class="table table-bordered" style="margin-top:-17px; margin-bottom:0px">
                        <tbody>
                            <tr>
                                <td width="10%"></td>
                                <td width="15%"></td>
                                <td width="45%"></td>                               
                                <td width="15%"><strong class="total_sales"></strong></td>
                                <td width="15%"></td>
                            </tr>
                            <tr>
                                <td width="10%"></td>
                                <td width="15%"></td>
                                <td width="45%"></td>                          
                                <td width="15%"><strong class="discounts">Less: SC/PWD<br><?= number_format($totals['total_sales'], 2) ?></strong></td>
                                <td width="15%"><?= number_format($totals['discount'], 2) ?></td>
                            </tr>                            
                        </tbody>
                    </table>

                    <!-- Totales con líneas -->
                    <table class="table table-bordered" style="margin-bottom:0px">
                        <tbody>
                            <tr>
                                <td><strong>TOTAL AMOUNT DUE:</strong></td>
                                <td width="15%"><?= number_format($totals['total_due'], 2) ?> </td>
                            </tr>
                        </tbody>
                    </table>

                </tbody>
            </table>           
    </div>

    <div class="invoice-footer" style="margin-top:20px">
        <?php if ($totals['is_fully_paid']): ?>
            <p><b>Note:</b> "This invoice has been fully paid."</p>
        <?php endif; ?>
    </div>
</div>


