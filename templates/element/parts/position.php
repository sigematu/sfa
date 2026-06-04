          <?= $this->Form->control('position', [
              'label' => __('Position'),
              'type' => 'select',
              'options' => [
                  POS_CEO => __('社長・代表'),
                  POS_EXECUTIVE => __('役員級'),
                  POS_DEPARTMENT_HEAD => __('部長級'),
                  POS_SECTION_MANAGER => __('次長・課長級'),
                  POS_TEAM_LEADER => __('主任級'),
                  POS_STAFF => __('一般職'),
              ],
          ]); ?>
