 /*
 * Модуль komtetKassaModule, подключается на странице настроек плагина. Скрипт будет запущен только тогда, когда страница админки будет готова к выводу настроек плагина.
 */
var komtetKassaModule = (function() {

  return {
    pluginName: "komtet-kassa", //название плагина

    init: function() {
      // Сохраняет базовые настроки

	  $('body').on('click', '.section-komtet-kassa .base-setting-save', function() {

        let prepaymentStatus = $(".base-settings-open select[name=prepayment_check_status]").val();
        let is_prepayment_check = prepaymentStatus != "false"

        $.ajax({
          type: "POST",
          url: mgBaseDir+"/ajaxrequest",
          data: {
            pluginHandler: 'komtet-kassa', // имя папки в которой лежит данный плагин
            actionerClass: "Pactioner",
            action: "saveBaseOption", // название действия в классе
            data:{
              shop_id : $(".base-settings-open input[name=shop_id]").val(),
              secret : $(".base-settings-open input[name=secret]").val(),
              queue_id : $(".base-settings-open input[name=queue_id]").val(),
              sno : $(".base-settings-open select[name=sno]").val(),
              is_print : $(".base-settings-open input[name=is_print]").prop('checked'),
              payments: komtetKassaModule.listOfPayments,

              prepayment_check_status : prepaymentStatus,
              fullpayment_check_status : $(".base-settings-open select[name=fullpayment_check_status]").val(),
              is_prepayment_check: is_prepayment_check,
            },
          },
          dataType: "json",
          success: function(response) {
            // Выводим нотификацию с результатом (ошибка или успех)
            admin.indication(response.status, response.msg);
            // Если успех, обновляем страницу настроек плагина
            if (response.status == "success") {
              admin.refreshPanel()
            }
          }
        });
      });
    },
  };
})();

$(document).ready(function() {
    komtetKassaModule.listOfPayments = [];
	komtetKassaModule.init();
});
