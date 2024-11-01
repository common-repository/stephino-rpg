<?php
/**
 * Template:Dialog:User Arena List / Page
 * 
 * @title      User Arena dialog - List Page
 * @desc       Template for listing available platformers (page)
 * @copyright  (c) 2021, Stephino
 * @author     Mark Jivko <stephino.team@gmail.com>
 * @package    stephino-rpg
 * @license    GPL v3+, https://gnu.org/licenses/gpl-3.0.txt
 */
!defined('STEPHINO_RPG_ROOT') && exit();

/* @var $ptfsList array */
?>
<?php if (!count($ptfsList)):?>
    <div class="col-12 text-center">
        <h5><?php 
            echo $userId == $arenaAuthorId
                ? esc_html__('It is time to create your first game!', 'stephino-rpg')
                : esc_html__('There are no games available', 'stephino-rpg');
        ?></h5>
    </div>
<?php else:?>
    <?php 
        foreach ($ptfsList as $ptfRow):
            // Get the author name
            $authorId = (int) $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_USER_ID];
            $authorName = $authorId > 0 
                ? Stephino_Rpg_Utils_Lingo::getUserName(Stephino_Rpg_Db::get()->tableUsers()->getById($authorId))
                : null;
    ?>
        <div class="col-6 col-md-4 col-lg-3" data-click="dialog" data-click-args="dialogUserArenaPlay,<?php echo (int) $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_ID];?>">
            <div class="ptf-card">
                <?php if ($userId == $authorId || Stephino_Rpg_Cache_User::get()->isElevated(Stephino_Rpg_Cache_User::PERM_MOD_PTFS)):?>
                    <div data-role="ptf-labels">
                        <?php if (Stephino_Rpg_Db_Table_Ptfs::PTF_VISIBILITY_PRIVATE === $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_VISIBILITY]): ?>
                            <span class="badge badge-warning"><?php echo esc_html__('Private', 'stephino-rpg');?></span>
                        <?php endif;?>
                        <?php 
                            if (Stephino_Rpg_Db_Table_Ptfs::PTF_VISIBILITY_PRIVATE !== $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_VISIBILITY]
                                && Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_APPROVED !== $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_REVIEW]):
                                foreach (Stephino_Rpg_Db::get()->modelPtfs()->getReviewLabels() as $reviewKey => $reviewLabel):
                                    if ($reviewKey != $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_REVIEW]) {
                                        continue;
                                    }
                                    $ptfReviewBadge = 'warning';
                                    $ptfReviewPrefix = '';
                                    switch ($reviewKey) {
                                        case Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_APPROVED:
                                            $ptfReviewBadge = 'primary';
                                            $ptfReviewPrefix = '&#9989;';
                                            break;

                                        case Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_PENDING:
                                            $ptfReviewBadge = 'info';
                                            $ptfReviewPrefix = '';
                                            break;

                                        case Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_SUSPENDED:
                                            $ptfReviewBadge = 'danger';
                                            $ptfReviewPrefix = '&#x1F6AB;';
                                            break;
                                    }
                        ?>
                            <span class="badge badge-<?php echo $ptfReviewBadge;?>">
                                <?php echo $ptfReviewPrefix;?>
                                <?php echo $reviewLabel;?>
                            </span>
                        <?php endforeach; endif;?>
                    </div>
                <?php endif;?>
                <div data-effect="ptfPreview" data-effect-args="<?php echo implode(',', $ptfRow[Stephino_Rpg_Db_Model_Ptfs::PTF_EXTRA_PREVIEW]);?>"></div>
                <div data-effect="ptfStars" data-effect-args="<?php echo round($ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_RATING], 2);?>"></div>
                <div data-role="ptf-played">
                    &#x1f3c1; <?php echo Stephino_Rpg_Utils_Lingo::isuFormat($ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_FINISHED]);?>
                    &#x1f3c6; <?php echo Stephino_Rpg_Utils_Lingo::isuFormat($ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_FINISHED_WON]);?>
                </div>
                <div data-role="ptf-name" title="<?php echo esc_attr($ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_NAME]);?>">
                    <?php echo esc_html($ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_NAME]);?>
                </div>
                <?php if (null !== $authorName):?>
                    <div data-role="ptf-author">
                        <?php echo esc_html__('by', 'stephino-rpg');?> <b><?php echo esc_html($authorName);?></b>
                    </div>
                <?php endif;?>
                <div data-role="ptf-won" title="<?php echo esc_attr__('Win rate', 'stephino-rpg');?>">
                    <?php 
                        $ptfSuccessRate = $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_FINISHED] > 0
                            ? abs(100 * $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_FINISHED_WON] / $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_FINISHED])
                            : 0;
                        echo number_format(
                            $ptfSuccessRate,
                            in_array($ptfSuccessRate, array(0, 100)) 
                                ? 0 
                                : 1
                        );
                    ?>%
                </div>
                <?php if ($ptfRow[Stephino_Rpg_Db_Model_Ptfs::PTF_EXTRA_REWARD]):?>
                <div data-role="ptf-reward" title="<?php echo esc_attr__('Reward available', 'stephino-rpg');?>">
                    <div class="res res-<?php echo Stephino_Rpg_Renderer_Ajax::RESULT_RES_GEM;?>">
                        <div class="icon"></div>
                        <span><?php echo number_format($ptfRow[Stephino_Rpg_Db_Model_Ptfs::PTF_EXTRA_REWARD]);?></span>
                    </div>
                </div>
                <?php endif;?>
            </div>
        </div>
    <?php endforeach;?>
    <div class="col-12 text-center">
        <?php 
            require Stephino_Rpg_Renderer_Ajax_Dialog::dialogTemplatePath(
                Stephino_Rpg_Renderer_Ajax_Dialog::TEMPLATE_COMMON_PAGINATION
            );
        ?>
    </div>
<?php endif;?>