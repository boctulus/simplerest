<?php

$cfg  = include __DIR__ . '/../config/config.php';

?>

<div class="fl-row-content-wrap">
    <div class="uabb-row-separator uabb-top-row-separator">
</div>
            <div class="fl-row-content fl-row-fixed-width fl-node-content">
    
<div class="fl-col-group fl-node-v842fx0rcp5y" data-node="v842fx0rcp5y">
      <div class="fl-col fl-node-xl1f5hqjbr0u" data-node="xl1f5hqjbr0u">
  <div class="fl-col-content fl-node-content"><div class="fl-module fl-module-rich-text fl-node-e0obf84pgy2r" data-node="e0obf84pgy2r">
  <div class="fl-module-content fl-node-content">
    <div class="fl-rich-text">
  <p></p><div class="ltar-tax-calculator">

  <h2 class="fl-post-title" itemprop="headline">Terra Tax Savings Calculator</h2>
  <p class="intro">Consult your Financial Advisor to ensure this investment is suitable for you.</p>
  <div class="print_holder"><a id="btn_print_ltartc" class="btn-print"><i class="fa fa-print" aria-hidden="true"></i> Print</a></div>
  <h3 class="form-title">Calculator Inputs</h3>

  <form class="ltar-tax-calculator-form">
    <div class="form-group">
      <div class="left-col">
        <label for="ltc-province" class="control-label"><span class="individual-text">Your Province</span><span class="company-ccpc-text" style="display: none;">Company Province</span></label>
      </div>
      <div class="right-col">
        <div class="hidden-value-lt">Ontario</div>
        <select class="form-control" id="ltc-province" name="ltc-province" required="">
          <option value="AB">Alberta</option>
          <option value="BC">British Columbia</option>
          <option value="MB">Manitoba</option>
          <option value="NS">Nova Scotia</option>
          <option selected="selected" value="ON">Ontario</option>
          <option value="QC">Quebec</option>
          <option value="SK">Saskatchewan</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <div class="left-col">
        <label for="ltc-investor-type" class="control-label"><span class="individual-text">Your Investor Type</span><span class="company-ccpc-text" style="display: none;">Investor Type</span></label>
      </div>
      <div class="right-col">
        <div class="hidden-value-lt">Individual</div>
        <select class="form-control" id="ltc-investor-type" name="ltc-investor-type" required="">
          <option selected="selected" value="Individual">Individual</option>
          <option value="Company - CCPC">Company - CCPC</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <div class="left-col">
        <label for="ltc-income" class="control-label"><span class="individual-text">Your Taxable Income ( ≥$105,000 ) - $</span><span class="company-ccpc-text" style="display: none;">Investment Income ( ≥$5,000 ) - $</span></label>
      </div>
      <div class="right-col">
        <div class="hidden-value-lt">250,000</div>
        <input type="text" class="form-control" id="ltc-income" name="ltc-income" value="250,000" required="">
        <div class="error"></div>
      </div>
    </div>
    <div class="form-group">
      <div class="left-col">
        <label for="ltc-investment" class="control-label"><span class="individual-text">Your Investment ( ≥$5,000 ) - $</span><span class="company-ccpc-text" style="display: none;">Investment ( ≥$5,000 ) - $</span></label>
      </div>
      <div class="right-col">
        <div class="hidden-value-lt">10,000</div>
        <input type="text" class="form-control" id="ltc-investment" name="ltc-investment" value="10,000" required="">
        <div class="error"></div>
      </div>
    </div>
    <div class="form-group">
      <div class="left-col">
        <label for="ltc-capital-losses" class="control-label"><span class="individual-text">Your Capital Losses available (if any) - $</span><span class="company-ccpc-text" style="display: none;">Capital Losses available (if any) - $</span></label>
      </div>
      <div class="right-col">
        <div class="hidden-value-lt">0</div>  
        <input type="text" class="form-control" id="ltc-capital-losses" name="ltc-capital-losses" value="0">
        <div class="error"></div>
      </div>
    </div>
    <div class="form-group">
      <div class="left-col">
        <label for="ltc-nav" class="control-label"><span class="individual-text">NAV on Redemption - %</span><span class="company-ccpc-text" style="display: none;">NAV on Redemption - %</span></label>
      </div>
      <div class="right-col">
        <div class="hidden-value-lt">80</div> 
        <input type="text" class="form-control" id="ltc-nav" name="ltc-nav" value="80" required="">
        <div class="error"></div>
      </div>
    </div>

    <!-- <div class="btn-submit-holder"><input type="submit" class="btn-submit" value="Submit"></div> -->

  </form>

  <div class="form-info-income">
    <span class="individual-text">Taxable income is gross income (salary, bonus, pension or interest income, 50% of a capital gain etc.) less RRSP deductions, interest expenses, etc &amp; exemptions.</span>
    <span class="company-ccpc-text" style="display: none;">Investment income for a CCPC includes interest income, foreign dividend income, rental income &amp; taxable capital gains.</span>
  </div>
  <div class="form-info-nav">NAV on Redemption is the expected future value of the investment expressed as a percentage.</div>

  <div class="ltc-results" style="display: block;">

    <div class="result-table">
      <table class="table">
        <thead>
          <tr>
            <th> Cash Flow &amp; Rate of Return</th> 
            <th><span>Year 1</span><br>Investment</th> 
            <th><span>Year 2</span><br>Rollover <sup>1</sup></th> 
            <th><span>Years 3 - 5</span><br>Post Rollover</th> 
          </tr> 
        </thead> 
        <tbody> 
          <tr> 
            <td class="unbold">Investment</td> 
            <td class="D18">($10,000)</td> 
            <td>-</td> 
            <td>-</td> 
          </tr> 
          <tr> 
            <td class="unbold">Annual Tax Savings <sup>2</sup></td> 
            <td class="D19">$7,057</td> 
            <td class="F19">($541)</td> 
            <td class="H19">$236</td> 
          </tr> 
          <tr> 
            <td class="unbold">NAV on Redemption</td> 
            <td>-</td> 
            <td class="F20">$8,000</td> 
            <td>-</td> 
          </tr>
          <tr> 
            <td class="unbold">Capital Gains Tax on Redemption<sup>3</sup></td> 
            <td>-</td> 
            <td class="F21">($2,141)</td> 
            <td>-</td> 
          </tr>
          <tr class="total-begins"> 
            <td>Cash Flow - $</td> 
            <td class="D22">($2,943)</td> 
            <td class="F22">$5,318</td> 
            <td class="H22">$236</td> 
          </tr>
          <tr> 
            <td>Cumulative Return - $</td> 
            <td class="D23">($2,943)</td> 
            <td class="F23">$2,375</td> 
            <td class="H23">$2,610</td> 
          </tr> 
          <tr> 
            <td>Rate of Return - %</td> 
            <td></td> 
            <td class="F24">80.7%</td> 
            <td class="H24">88.7%</td>
          </tr>
        </tbody> 
      </table>

      <div class="disclaimer-container">
        <p class="disclaimer">Rate of return for <span class="ltc_calc_rate_of_return">88.7%</span> is calculated as the total Cumulative Return for <span class="ltc_calc_cumulative_return">$2,610</span> divided by the Capital at Risk in year 1 for <span class="ltc_calc_1_year">$2,943</span>.</p>
        <p class="disclaimer"><sup>1</sup> Rollover is targeted for June in the 2nd calendar year. <sup>2, 3</sup> See following tables.</p>
      </div>
    </div>

    <div class="result-table">
      <table class="table">
        <thead>
          <tr>
            <th><sup>2</sup> Tax Savings</th> 
            <th><span>Year 1</span><br>Investment</th> 
            <th><span>Year 2</span><br>Rollover</th> 
            <th><span>Years 3 - 5</span><br>Post Rollover</th> 
          </tr> 
        </thead> 
        <tbody> 
          <tr> 
            <td class="unbold">A. CEE &amp; Other Deductions</td> 
            <td class="D32">$9,400</td> 
            <td class="F32">$1,015</td> 
            <td class="H32">$440</td> 
          </tr> 
          <tr class="add-border"> 
            <td class="unbold">B. Average Tax Rate <sup>4</sup></td> 
            <td class="D33">53.53%</td> 
            <td class="F33">53.53%</td> 
            <td class="H33">53.53%</td> 
          </tr> 
          <tr> 
            <td class="unbold">C. Tax Savings - CEE &amp; Other ( A x B )</td> 
            <td class="D34">$5,032</td> 
            <td class="F34">$543</td> 
            <td class="H34">$236</td> 
          </tr>
          <tr> 
            <td class="unbold">D. Tax Savings - METC <sup>5, 6</sup></td> 
            <td class="D35">$675</td> 
            <td class="F35">($361)</td> 
            <td>-</td> 
          </tr>
          <tr> 
            <td class="unbold">E. Tax Savings -  CMETC <sup>5, 6</sup></td> 
            <td class="D36">$1,350</td> 
            <td class="F36">($723)</td> 
            <td>-</td> 
          </tr>
          <tr class="total-begins"> 
            <td>Annual Tax Savings ( C + D + E )</td> 
            <td class="D37">$7,057</td> 
            <td class="F37">($541)</td> 
            <td class="H37">$236</td> 
          </tr>
        </tbody> 
      </table>

      <div class="disclaimer-container">
        <p class="disclaimer"><sup>4</sup> Average tax rate is the average rate over multiple income tax brackets &amp; determines the value of CEE &amp; Other deductions.</p>
        <p class="disclaimer"><sup>5</sup> METC is the federal 15% Mineral Exploration Tax Credit; CMETC is the federal 30% Critcal Mineral Exploration Tax Credit.</p>
        <p class="disclaimer"><sup>6</sup> METC &amp; CMETC are only available to individuals (and not corporations) and taxable as income in year 2.</p>
      </div>
    </div>

    <div class="result-table">
      <table class="table">
        <thead>
          <tr>
            <th><sup>3</sup> Capital Gains Tax on Redemption</th> 
            <th><span>Year 2</span><br>Rollover</th> 
          </tr> 
        </thead> 
        <tbody> 
          <tr> 
            <td class="unbold">A. NAV on Redemption</td>
            <td class="F46">$8,000</td> 
          </tr> 
          <tr> 
            <td class="unbold">B. Less: Eligible Capital Loss Carry Forward</td> 
            <td class="F47">$0</td> 
          </tr> 
          <tr> 
            <td class="unbold">C. Net Taxable Capital Gain ( A - B )</td> 
            <td class="F48">$8,000</td> 
          </tr>
          <tr> 
            <td class="unbold">D. Tax Rate on Capital Gain (50% inclusion rate)</td> 
            <td class="F49">26.77%</td> 
          </tr>
          <tr class="total-begins"> 
            <td>Capital Gains tax on Redemption ( C x D )</td> 
            <td class="F50">$2,141</td> 
          </tr>
        </tbody> 
      </table>

    </div>

  </div>

  <div class="disclaimer-container disclaimer-container-footer">
    <p class="disclaimer">This tax calculator is of a general nature only and does not constitue an offer or solicitation, nor should it be construed to be legal or tax advice to any investor, and no representations with respect to the tax consequences to any investor are made. The information contained herein, while obtained from sources that are believed to be reliable, is not guaranteed as to accuracy or completeness. Your personal tax situation may be beyond the scope of this calculator and should not be relied upon without professional advice. The figures are not intended as a forecast of future events. Actual tax deductions and tax credits may be more or less. The calculations assume alternative minimum tax does not apply. Tax rates and capital gains inclusion are also subject to change. The tax calculator utilizes 2022 tax rates.</p>
    <p class="disclaimer">This offering is available only to qualified investors in Canada who must receive a Offering Memorandum prior to subscribing for Units. Investors should read and undestand the risk factors prior to making an investment. Terra Fund Management Ltd, and any related parties assume no responsibility for any losses or damages, whether direct or indirect, which arise out of the use of this information. Each prospective investor should obtain independent advice from an expert tax advisor who is knowledgeable in the income tax considerations applicable to investing in flow-through limited partnerships based on the investor’s own personal situation.</p>
  </div>

</div><p></p>
</div>
  </div>
</div>
</div>
</div>
  </div>
    </div>
  </div>