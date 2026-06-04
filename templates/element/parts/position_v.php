                        <?php
                            $positions = [
                                POS_CEO => __('社長・代表'),
                                POS_EXECUTIVE => __('役員級'),
                                POS_DEPARTMENT_HEAD => __('部長級'),
                                POS_SECTION_MANAGER => __('次長・課長級'),
                                POS_TEAM_LEADER => __('主任級'),
                                POS_STAFF => __('一般職'),
                            ];
                            
                            $posValue = null;
                            if (isset($bpContact)) $posValue = $bpContact->position;
                            elseif (isset($clientContact)) $posValue = $clientContact->position;
                            elseif (isset($position)) $posValue = $position;

                            echo isset($positions[$posValue]) ? $positions[$posValue] : h($posValue);
                        ?>
