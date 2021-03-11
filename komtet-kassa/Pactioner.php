<?php

/**
 * Класс Pactioner предназначен для выполнения действий,  AJAX запросов плагина
 *
 */
class Pactioner extends Actioner {

  private $pluginName = 'komtet-kassa'; // Имя плагина

  /**
   * Сохраняет опции плагина
   * @return boolean
   */
  public function saveBaseOption() {
    $this->messageSucces = 'Сохранено';
    $this->messageError = 'Ошибка сохранения';
    var_dump($_POST['data']);
    die();
    if (!empty($_POST['data'])) {
      // Устанавливаем новые опции
      MG::setOption(array('option' => 'komtet-kassa-option', 'value' => addslashes(serialize($request['data']))));
    }

    return true;
  }
}