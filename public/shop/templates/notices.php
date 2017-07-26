<?php foreach ($this->errors as $error): ?>
    <div class="alert alert-danger alert-dismissable">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <span class="glyphicon glyphicon-alert"></span>
      <
    </div>
<?php endforeach; ?>
<?php foreach ($this->errors as $error): ?>
    <div class="alert alert-success alert-dismissable">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      {$notice}
    </div>
<?php endforeach; ?>