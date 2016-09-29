<div class="custom_text wjmljc_custom_job_count">
      <ul class="wjmljc_custom_cls">
       <?php if(get_option('wjmljc_show_job') && get_option('wjmljc_show_job')==1): ?>
         <li>   
           <img src="<?php echo plugins_url('assets/images/14706671-small.png',__FILE__ ); ?>"/>
           <strong><?php echo esc_html('New Jobs:');?>&nbsp;<span class="wjmljc_count_job"></span></strong>
        </li>
       <?php endif; if(get_option('wjmljc_show_company') && get_option('wjmljc_show_company')==1):?>
         <li>
          <img src="<?php echo plugins_url('assets/images/14706671-small.png',__FILE__ ); ?>"/>
          <strong><?php echo esc_html('Company:');?>&nbsp;<span class="wjmljc_count_company"></span></strong>
         <li/>
       <?php endif; if(get_option('wjmljc_show_seeker') && get_option('wjmljc_show_seeker')==1): ?>
         <li>
          <img src="<?php echo plugins_url('assets/images/14706671-small.png',__FILE__ ); ?>"/>
          <strong><?php echo esc_html('Job Seeker:');?>&nbsp;<span class="wjmljc_count_seeker"></span></strong>
        </li>
       <?php endif; ?>
      </ul>
      <input type="hidden" id="wpjmljc_ajax_active" value="wpjmljc_002"/>
</div>