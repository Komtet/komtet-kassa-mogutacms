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
	      <span>Печатать чек:</span>
		</div>

		<div class="large-6 columns checkbox margin">
		  <input type="checkbox" name="is_print" id="isPrint" <?php echo ($options['is_print']=='true'?'checked':'') ?>>
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
		    <option value="false" <?php echo ($options['prepayment_check_status'] == "false" ? 'selected' : ''); ?>>Не выдавать</option>
		    <option value=2 <?php echo ($options['prepayment_check_status'] == 2 ? 'selected' : ''); ?>>Оплачен</option>
		  </select>
		</div>
	  </div>

	  <div class="row">
	    <div class="large-6 columns dashed">
	      <span>Чек полного расчёта:</span>
	      <i class="fa fa-question-circle tip fl-right" title="Необходимо выбрать статус заказа, по которому будет формироваться чек полного расчёта"></i>
		</div>

		<div class="large-6 columns checkbox margin">
		  <select name="fullpayment_check_status" id="fullpayment_check_status" onfocus="selectedFullpaymentStatus()">>
		    <option value=2 id='paid' <?php echo ($options['fullpayment_check_status'] == 2 ? 'selected' : ''); ?>>Оплачен</option>
		    <option value=3 id='shipped' <?php echo ($options['fullpayment_check_status'] == 3 ? 'selected' : ''); ?>>В доставке</option>
		    <option value=5 id='delivered' <?php echo ($options['fullpayment_check_status'] == 5 ? 'selected' : ''); ?>>Выполнен</option>
		  </select>
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
  function selectedPrepaymentStatus(selectItem) {
    var selected_value = selectItem.value;
    if (selected_value == "false") {
      document.getElementById("shipped").style.display = 'none';
      document.getElementById("delivered").style.display = 'none';
      document.getElementById("fullpayment_check_status").value = 2;
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
      document.getElementById("fullpayment_check_status").value = 2;
    } else {
      document.getElementById("shipped").style.display = 'block';
      document.getElementById("delivered").style.display = 'block';
      document.getElementById("paid").style.display = 'none';
    }
  }
</script>
