<?php

class Pactioner extends Actioner {
 /**
  * Сохранение опций
  */
  public function saveBaseOption() {
    $this->messageSucces = 'Сохранено';
    $this->messageError = 'Ошибка сохранения';
    if (!empty($_POST['data'])) {
      MG::setOption(array('option' => 'atolOption', 'value' => addslashes(serialize($_POST['data']))));
    }
    return true;
  }

  public function setCountPrintRows() {
    if (is_numeric($_POST['count'])&&!empty($_POST['count'])) {
      $count = $_POST['count'];
    }
    MG::setOption(array('option' => 'countPrintRowsAtol', 'value' => $count));
    return true;
  }

  public function refund() {
    $tmp = atol::refund($_POST['id']);
    if ($tmp === true) {
      return true;
    } else {
      $this->messageError = $tmp;
      return false;
    }
  }
}