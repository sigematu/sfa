<div class="user-panel mt-3 pb-3 mb-3 d-flex">
  <!-- <div class="image">
    <?= $this->Html->image('CakeLte./AdminLTE/dist/img/user2-160x160.jpg', ['class'=>'img-circle elevation-2', 'alt'=>'User Image']) ?>
  </div> -->
  <div class="info">
    <div class="bg-gray-dark color-palette">
      <?= h($user->full_name ?? '') ?><br>
      <?= h($user->email ?? '') ?><br>
    </div>
  </div>
</div>
