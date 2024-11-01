    jQuery(document).ready(function(){
		jQuery('.options').slideUp();
		
		jQuery('.subdivision h3').click(function(){		
			if(jQuery(this).parent().next('.options').css('display')=='none')
				{	jQuery(this).removeClass('close');
					jQuery(this).addClass('open');
					jQuery(this).children('img').removeClass('close');
					jQuery(this).children('img').addClass('open');
					
				}
			else
				{	jQuery(this).removeClass('open');
					jQuery(this).addClass('close');		
					jQuery(this).children('img').removeClass('open');			
					jQuery(this).children('img').addClass('close');
				}
				
			jQuery(this).parent().next('.options').slideToggle('slow');	
		});
});