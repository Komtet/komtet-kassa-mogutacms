<div class="section-atol">

	<div class="widget-table-action">
		<button class="base-setting-open button"><span><i class="fa fa-cogs"></i></i> Настройки</span>
		</button>
		<div class="pagi fl-right">
			<span class="last-items">Чеков на странице </span>
			<select class="last-items-dropdown countPrintRows small">
				<?php
				foreach(array(5, 10, 15, 20, 25, 30, 100, 150, 250) as $value){
				$selected = '';
				if($value == $countPrintRowsAtol){
					$selected = 'selected="selected"';
				}
				echo '<option value="'.$value.'" '.$selected.' >'.$value.'</option>';
			}
			?>
		 </select>
		</div>
		<div class="clear"></div>
	</div>

	<div class="widget-table-action base-settings" style="display:none;">
		<h3>Настройки сервиса atol.ru</h3>
		<div class="large-6 small-12 columns">
			<!-- <div class="row">
                <div class="large-6 columns dashed">
					<span>Версия протокола:</span>
				</div>
				<div class="large-6 columns">
					<select name="apiVers">
						<option value="v3" <?php echo ($options['apiVers'] == 'v3' ? 'selected' : ''); ?>>v3</option>
						<option value="v4" <?php echo ($options['apiVers'] == 'v4' ? 'selected' : ''); ?>>v4</option>
					</select>
				</div>
			</div> -->
			<div class="row">
                <div class="large-6 columns dashed">
					<span>Логин в сервисе atol.ru:</span>
				</div>
				<div class="large-6 columns">
					<input type="text" name="atolLogin" value="<?php echo $options['atolLogin']; ?>">
				</div>
			</div>
			<div class="row">
                <div class="large-6 columns dashed">
					<span>Пароль от сервиса atol.ru:</span>
				</div>
				<div class="large-6 columns">
					<input type="text" name="atolPass" value="<?php echo $options['atolPass']; ?>">
				</div>
			</div>
			<div class="row">
                <div class="large-6 columns dashed">
					<span>Группа в сервисе atol.ru:</span>
				</div>
				<div class="large-6 columns">
					<input type="text" name="atolGroup" value="<?php echo $options['atolGroup']; ?>">
				</div>
			</div>
			<div class="row">
                <div class="large-6 columns dashed">
					<span>ИНН организации:</span>
				</div>
				<div class="large-6 columns">
					<input type="text" name="inn" value="<?php echo $options['inn']; ?>">
				</div>
			</div>
			<div class="row">
                <div class="large-6 columns dashed">
					<span>Адрес сайта (адрес места расчетов):</span>
				</div>
				<div class="large-6 columns">
					<input type="text" name="payment_address" value="<?php echo $options['payment_address']; ?>">
				</div>
			</div>
			<div class="row">
                <div class="large-6 columns dashed">
					<span>Email продавца:</span>
				</div>
				<div class="large-6 columns">
					<input type="text" name="seller_email" value="<?php echo $options['seller_email']; ?>">
				</div>
			</div>
			<div class="row">
                <div class="large-6 columns dashed">
					<span>Система налогообложения:</span>
				</div>
				<div class="large-6 columns">
					<select name="sno">
							<option value="osn" <?php echo ($options['sno'] == 'osn' ? 'selected' : ''); ?>>Общая СН</option>
							<option value="usn_income" <?php echo ($options['sno'] == 'usn_income' ? 'selected' : ''); ?>>Упрощенная СН (доходы)</option>
							<option value="usn_income_outcome" <?php echo ($options['sno'] == 'usn_income_outcome' ? 'selected' : ''); ?>>Упрощенная СН (доходы организации минус расходы)</option>
							<option value="envd" <?php echo ($options['sno'] == 'envd' ? 'selected' : ''); ?>>Единый налог на вмененный доход</option>
							<option value="esn" <?php echo ($options['sno'] == 'esn' ? 'selected' : ''); ?>>Единый сельскохозяйственный налог</option>
							<option value="patent" <?php echo ($options['sno'] == 'patent' ? 'selected' : ''); ?>>Патентная СН</option>
					</select>
				</div>
			</div>
			<div class="row">
                <div class="large-6 columns dashed">
					<span>НДС, включенный в цену:</span>
				</div>
				<div class="large-6 columns">
					<select name="tax">
							<option value="none" <?php echo ($options['tax'] == 'none' ? 'selected' : ''); ?>>Без НДС</option>
							<option value="vat0" <?php echo ($options['tax'] == 'vat0' ? 'selected' : ''); ?>>0%</option>
							<option value="vat10" <?php echo ($options['tax'] == 'vat10' ? 'selected' : ''); ?>>10%</option>
							<option value="vat20" <?php echo ($options['tax'] == 'vat20' ? 'selected' : ''); ?>>20%</option>
							<option value="vat110" <?php echo ($options['tax'] == 'vat110' ? 'selected' : ''); ?>>10/110%</option>
							<option value="vat120" <?php echo ($options['tax'] == 'vat120' ? 'selected' : ''); ?>>20/120%</option>
					</select>
				</div>
			</div>
			<div class="row">
                <div class="large-6 columns dashed">
					<span>Исключить способы оплаты:</span><br><a id="clearPayment">Очистить</a>
				</div>
				<div class="large-6 columns">
					<select name="paymentDisable" multiple>
						<?php foreach($paymentVariants as $key => $value): ?>
							<option value="<?php echo $key; ?>"<?php if(in_array($key, $options['paymentDisable'])) echo " selected" ?>><?php echo $value; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="large-6 columns dashed">
					<span>Тестовый режим:</span>
				</div>
				<div class="large-6 columns checkbox margin">
					<input type="checkbox" name="testMode" id="testMode" <?php echo ($options['testMode']=='true'?'checked':'') ?>>
					<label for="testMode"></label>
				</div>
			</div>
			<div class="row">
                <div class="large-6 columns dashed">
					<span>Префикс внешнего номера заказа:</span>
					<i class="fa fa-question-circle tip fl-right" title="Заполните это поле, если у вас возникает ошибка 'В системе существует чек с external_id'"></i>
				</div>
				<div class="large-6 columns margin">
					<input type="text" name="prefix" value="<?php echo $options['prefix']; ?>">
				</div>
			</div>
			<div class="row">
				<div class="large-6 columns">
				</div>
				<div class="large-6 columns">
					<button class="base-setting-save button success"><span><i class="fa fa-floppy-o"></i> Сохранить</span></button>
				</div>
			</div>
		</div>		
		<div class="clear"></div>
	</div>

	<div>
		<h4>Таблица электронных чеков atol</h4>
		<table class="main-table" style="width:100%;">
			<thead>
				<tr>
					<th>ID заказа</th>
					<th>Номер заказа</th>
					<th>Номер чека на atol.ru (uuid)</th>              
					<th>Статус</th>
					<th>Время</th>
					<th>Фискальный признак документа или текст ошибки</th>
					<th>Действия</th>
				</tr>
			</thead>
			<tbody class="comments-tbody">
				<?php if(!empty($sales)){ ?>
					<?php foreach($sales as $sale): ?>
						<tr data-id="<?php echo $sale['id'] ?>">
							<td><?php echo $sale['id']; ?></td>
							<td><?php echo $sale['name']; ?></td>
							<td><?php echo $sale['uuid']; ?></td>
							<td class="status">
								<?php 
								switch ($sale['status']) {
									case 'wait':
										echo 'выполняется';
										break;
									case 'done':
										echo '<span style="color:green;">завершено<span>';
										break;
									case 'done_refund':
										echo '<span style="color:green;">возврат завершен<span>';
										break;
									case 'error':
									case 'fail':
										echo '<span style="color:red;">ошибка<span>';
										break;
									case 'error_refund':
									case 'fail_refund':
										echo '<span style="color:red;">ошибка возврата<span>';
										break;
									
									default:
										echo $sale['status'];
										break;
								}
								?>
							</td>
							<td><?php echo date("d.m.Y H:i:s", strtotime($sale['time'])); ?></td>
							<td class="status_text">
								<?php
								if (is_numeric($sale['fn_number'])) {
									echo $sale['fn_number'];
								} elseif($sale['status'] == 'wait') {
									echo 'Ожидание ответа от сервиса atol';
								} else {
									echo '<span style="color:red;">произошла ошибка <span><a class="moreInfo" txt="'.htmlspecialchars($sale['fn_number']).'">подробнее</a>';
								}
								?>
							</td>
							<td>
								<?php if ($sale['status'] == 'done' && !empty($sale['request'])) { ?>
									<span class="refund" title="Возврат заказа"><i class="fa fa-recycle"></i></span>
								<?php } ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php } else { ?>
				<tr>
					<td colspan="6">Чеки отсутствуют</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="table-pagination fl-right"><?php echo $pagination ?></div>
	<div class="clear"></div>

</div>