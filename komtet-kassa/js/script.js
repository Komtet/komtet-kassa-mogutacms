var atolModule = (function() {
	
	return { 
		init: function() {			
			// Сохраняет базовые настроки
			$('body').on('click', '.section-atol .base-setting-save', function() {

				//удаление пробелов
				var site = $(".base-settings input[name=payment_address]").val();
				site = site.replace( /\s/g, "");

				$.ajax({
					type: "POST",
					url: mgBaseDir+"/ajaxrequest",
					data: {
						pluginHandler: 'atol', // имя папки в которой лежит данный плагин
						actionerClass: "Pactioner", 
						action: "saveBaseOption", // название действия в классе 
						data:{
							apiVers : $(".base-settings select[name=apiVers]").val(),
							atolLogin : $(".base-settings input[name=atolLogin]").val(),
							atolPass : $(".base-settings input[name=atolPass]").val(),
							atolGroup : $(".base-settings input[name=atolGroup]").val(),
							inn : $(".base-settings input[name=inn]").val(),
							payment_address : site,
							seller_email : $(".base-settings input[name=seller_email]").val(),
							sno : $(".base-settings select[name=sno]").val(),
							tax : $(".base-settings select[name=tax]").val(),
							paymentDisable : $(".base-settings select[name=paymentDisable]").val(),
							testMode: $(".base-settings input[name=testMode]").prop('checked'),
							prefix: $(".base-settings input[name=prefix]").val(),
						},
					},
					dataType: "json",
					success: function(response){
						admin.indication(response.status, response.msg);
					}
				});
			});

			$('body').on('click', '.section-atol .refund', function() {
				if (!confirm('Зарегистрировать возврат?')) {return false;}
				var tr = $(this).closest('tr');
				var id = tr.data('id');

				admin.ajaxRequest({
						mguniqueurl: "action/refund",
						pluginHandler: "atol",
						id: id,
					},
					function (response) {
						admin.indication(response.status, response.msg);
						if (response.status == 'success') {
							tr.find('.refund').hide();
							tr.find('.status').html('выполняется');
							tr.find('.status_text').html('Ожидание ответа от сервиса atol');
						}
					}
				);
			});

			$('body').on('click', '.section-atol .moreInfo', function() {
				$(this).parent().html($(this).attr('txt'));
			});

			$('body').on('click', '.section-atol #clearPayment', function() {
				$("select[name=paymentDisable] option:selected").prop("selected", false);
			});

			$('body').on('change', '.section-atol .countPrintRows', function(){

				var count = $(this).val();
				admin.ajaxRequest({
					pluginHandler: 'atol', // имя папки в которой лежит данный плагин
					actionerClass: "Pactioner", // в папке плагина
					action: "setCountPrintRows", // название действия в пользовательском	классе 
					count: count
				},
				function(response) {
					admin.refreshPanel();
				});
			});

			// Показывает панель с настройками.
			$('body').on('click', '.section-atol .base-setting-open', function() {
				$('.base-settings').slideToggle();
			});
		},
	};
})();

$(document).ready(function() {
	atolModule.init();
});