<div class="section-komtet-kassa">
  <div class="widget-table-action base-settings-open">
    <h3>Настройки сервиса КОМТЕТ Касса</h3>
    <div class="large-6 small-12 columns">
	  <div class="row">
	    <div class="large-6 columns dashed">
	      <span>ID магазина:</span>
	    </div>

	    <div class="large-6 columns">
	      <input type="text" name="shop_id" value="<?php echo $options['shop_id']; ?>">
	    </div>
	  </div>

	  <div class="row">
	    <div class="large-6 columns dashed">
	      <span>Секретный ключ магазина</span>
	    </div>

	    <div class="large-6 columns">
	      <input type="text" name="secret" value="<?php echo $options['secret']; ?>">
	    </div>
	  </div>

	  <div class="row">
	    <div class="large-6 columns dashed">
	      <span>ID очереди:</span>
	    </div>

	    <div class="large-6 columns">
	      <input type="text" name="queue_id" value="<?php echo $options['queue_id']; ?>">
	    </div>
	  </div>

	  <div class="row">
	    <div class="large-6 columns dashed">
	      <span>СНО:</span>
	    </div>

	    <div class="large-6 columns">
	      <select name="sno">
	        <option value=0 <?php echo ($options['sno'] == 0 ? 'selected' : ''); ?>>Общая СН</option>
	        <option value=1 <?php echo ($options['sno'] == 1 ? 'selected' : ''); ?>>Упрощенная СН (доход)</option>
	        <option value=2 <?php echo ($options['sno'] == 2 ? 'selected' : ''); ?>>Упрощенная СН (доходы организации минус расходы)</option>
	        <option value=3 <?php echo ($options['sno'] == 3 ? 'selected' : ''); ?>>Единый налог на вмененный доход</option>
	        <option value=4 <?php echo ($options['sno'] == 4 ? 'selected' : ''); ?>>Единый сельскохозяйственный налог</option>
	        <option value=5 <?php echo ($options['sno'] == 5 ? 'selected' : ''); ?>>Патентная СН</option>
	      </select>
	    </div>
	  </div>

	  <div class="row">
	    <div class="large-6 columns dashed">
	      <span>НДС, включенный в цену:</span>
	    </div>

	    <div class="large-6 columns">
	      <select name="vat">
            <option value="no" <?php echo ($options['vat'] == 'no' ? 'selected' : ''); ?>>Без НДС</option>
            <option value="0" <?php echo ($options['vat'] == '0' ? 'selected' : ''); ?>>0%</option>
            <option value="10" <?php echo ($options['vat'] == '10' ? 'selected' : ''); ?>>10%</option>
            <option value="20" <?php echo ($options['vat'] == '20' ? 'selected' : ''); ?>>20%</option>
            <option value="110" <?php echo ($options['vat'] == '110' ? 'selected' : ''); ?>>10/110</option>
            <option value="120" <?php echo ($options['vat'] == '120' ? 'selected' : ''); ?>>20/120</option>
	      </select>
	    </div>
	  </div>

      <div class="row">
	    <div class="large-6 columns dashed">
	      <span>Ставка НДС для доставки:</span>
	    </div>

	    <div class="large-6 columns">
	      <select name="vat_delivery">
            <option value="no" <?php echo ($options['vat_delivery'] == 'no' ? 'selected' : ''); ?>>Без НДС</option>
            <option value="0" <?php echo ($options['vat_delivery'] == '0' ? 'selected' : ''); ?>>0%</option>
            <option value="10" <?php echo ($options['vat_delivery'] == '10' ? 'selected' : ''); ?>>10%</option>
            <option value="20" <?php echo ($options['vat_delivery'] == '20' ? 'selected' : ''); ?>>20%</option>
            <option value="110" <?php echo ($options['vat_delivery'] == '110' ? 'selected' : ''); ?>>10/110</option>
            <option value="120" <?php echo ($options['vat_delivery'] == '120' ? 'selected' : ''); ?>>20/120</option>
	      </select>
	    </div>
	  </div>

	  <div class="row checkbox-field-margin">
	    <div class="large-6 columns dashed">
	      <span>Печатать чек:</span>
		</div>

		<div class="large-6 columns margin">
		  <input class="checkbox-margin" type="checkbox" name="is_print" id="isPrint" <?php echo ($options['is_print']=='true'?'checked':'') ?>>
		  <label for="isPrint"></label>
		</div>
	  </div>

	  <div class="row">
	    <div class="large-6 columns dashed">
		  <span>Чек предоплаты:</span>
		  <i class="fa fa-question-circle tip fl-right" title="Необходимо выбрать статус заказа, по которому будет формироваться чек предоплаты"></i>
		</div>

		<div class="large-6 columns checkbox margin">
		  <select name="prepayment_check_status" onChange="selectedPrepaymentStatus(this)" id="prepayment_check_status">
		    <option value='false' <?php echo ($options['prepayment_check_status'] == 'false' ? 'selected' : ''); ?>>Не выдавать</option>
		    <option value='paid' <?php echo ($options['prepayment_check_status'] == 'paid' ? 'selected' : ''); ?>>Оплачен</option>
		  </select>
		</div>
	  </div>

	  <div class="row">
	    <div class="large-6 columns dashed">
	      <span>Чек полного расчёта:</span>
	      <i class="fa fa-question-circle tip fl-right" title="Необходимо выбрать статус заказа, по которому будет формироваться чек полного расчёта"></i>
		</div>

		<div class="large-6 columns checkbox margin">
		  <select name="fullpayment_check_status" id="fullpayment_check_status" onfocus="selectedFullpaymentStatus()">
		    <option value='paid' id='paid' <?php echo ($options['fullpayment_check_status'] == 'paid' ? 'selected' : ''); ?>>Оплачен</option>
		    <option value=3 id='shipped' <?php echo ($options['fullpayment_check_status'] == 3 ? 'selected' : ''); ?>>В доставке</option>
		    <option value=5 id='delivered' <?php echo ($options['fullpayment_check_status'] == 5 ? 'selected' : ''); ?>>Выполнен</option>
		  </select>
		</div>
	  </div>

	  <div class="row">
        <div class="large-12 columns">
          <div class="row sett-line">
            <ul class="accordion test" data-accordion="" data-multi-expand="true" >
              <li class="accordion-item" data-accordion-item="">
                <a class="accordion-title content_blog_acc" href="javascript:void(0);" style="background: #e6e6e6;">Настройки способов оплаты</a>
                <div class="accordion-content" id="html-content-wrapper">
                  <?php foreach($paymentVariants as $key => $value): ?>
                    <div class="large-6 columns">
                      <input type="checkbox"
                             id=<?php echo ("payments.".$value['hash'].".id")?>
                             onchange="setDisabled('<?php echo ($value['hash'])?>', '<?php echo ($key)?>')"
                             <?php echo ($value['active'] == true ? 'checked':'') ?>>
                      <label for=<?php echo ("payments.".$value['hash'].".id")?>><?php echo ($value['name'])?></label>

                    </div>

                    <div class="large-6 columns">
                      <select class="paymentMethodSelect"
                              name=<?php echo ($key)?>
                              id=<?php echo ("payments.".$value['hash'].".type")?>
                              onChange="setValue({payId: '<?php echo ($key)?>', option: value})"
                              <?php echo ($value['active'] == true ? '':'disabled') ?>>
                        <option value=0>Не выбрано</option>
                        <option value="Безналичный рассчет" <?php echo ($value['option'] == 'Безналичный рассчет' ? 'selected' : ''); ?>>Безналичный рассчет</option>
                        <option value="Наличный рассчет" <?php echo ($value['option'] == 'Наличный рассчет' ? 'selected' : ''); ?>>Наличный рассчет</option>
                      </select>
                    </div>
                  <?php endforeach; ?>
                </div>
                <div class="clearfix"></div>
              </li>
            </ul>
          </div>
        </div>
	  </div>

	  <div class="row">
	    <div class="large-6 columns"></div>
	    <div class="large-6 columns">
	      <button class="base-setting-save button success"><span><i class="fa fa-floppy-o"></i> Сохранить</span></button>
	    </div>
	  </div>
	</div>
	<div class="clear"></div>
  </div>
</div>

<script>
  $(document).ready(function() {
     let paymentsSelects = $('.paymentMethodSelect');

     let i = 0;
     while(paymentsSelects[i]) {
        setValue({payId: paymentsSelects[i].name, option: paymentsSelects[i].value})
        i++;
     }
  });

  function setValue(value) {
    if (value.option === "0") {
      komtetKassaModule.listOfPayments.forEach((item, i) => {
        if (item.payId === value.payId) {
            komtetKassaModule.listOfPayments.splice(i, 1);
        }
      });
    } else {
      let doPush = true;
      komtetKassaModule.listOfPayments.forEach((item) => {
        if (item.payId === value.payId) {
          item.option = value.option;
          doPush = false;

          return;
        }
      });

      if (doPush) {
        komtetKassaModule.listOfPayments.push(value);
      }
    }
  }

  function selectedPrepaymentStatus(selectItem) {
    var selected_value = selectItem.value;
    if (selected_value == "false") {
      document.getElementById("shipped").style.display = 'none';
      document.getElementById("delivered").style.display = 'none';
      document.getElementById("fullpayment_check_status").value = 'paid';
    } else {
      document.getElementById("shipped").style.display = 'block';
      document.getElementById("delivered").style.display = 'block';
      document.getElementById("fullpayment_check_status").value = 5;
    }
  }

  function selectedFullpaymentStatus() {
    status_check_prepayment_value = document.getElementById("prepayment_check_status").value;
    if (status_check_prepayment_value == "false") {
      document.getElementById("shipped").style.display = 'none';
      document.getElementById("delivered").style.display = 'none';
      document.getElementById("paid").style.display = 'block';
      document.getElementById("fullpayment_check_status").value = 'paid';
    } else {
      document.getElementById("shipped").style.display = 'block';
      document.getElementById("delivered").style.display = 'block';
      document.getElementById("paid").style.display = 'none';
    }
  }

  function setDisabled(hash_name, key) {
    var field_id = document.getElementById('payments.' + hash_name + '.id');
    var field_type = document.getElementById('payments.' + hash_name + '.type');

    field_type.disabled = !field_id.checked;
    field_type.value = 0;

    komtetKassaModule.listOfPayments.forEach((item, i) => {
      if (item.payId === key) {
        komtetKassaModule.listOfPayments.splice(i, 1);
      }
    });
  }
</script>
