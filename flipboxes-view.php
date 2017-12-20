<?php 
	if($single_f_c!==""){
		$flipbox_color_scheme=$single_f_c;
	}else{
		$flipbox_color_scheme=$skincolor;
	}
	
echo'<div class="'.$cols.'">';
echo'<div data-effect="'.$effect.'" class="flip">';	
echo'<div class="'.$effect.'">';
if ($flip_layout=="dashed-with-icon")

	{
		?>
		<div class="face front" style="background: rgb(255, 255, 255); border-style: dashed; border-width: 4px; border-color:<?php echo "$flipbox_color_scheme";?>;"> 

		    <div class="ifb-flip-box-section ">
            <div class="flip-box-icon_default">
		    <div class="ult-just-icon-wrapper">
		    <div class="align-icon">

			<div class="aio-icon-img " style="font-size:<?php echo $icon_size;
				?>!important; color:<?php echo $flipbox_color_scheme;?>;display:inline-block;">
			<i class="fa <?php echo "$flipbox_icon";?>"></i>
			</div>
			</div>
		    </div>
			</div>
			<div class="flip-title" style="color:<?php echo $flipbox_color_scheme;?>;"><strong><?php echo $flipbox_title;?></strong></div>
			<div class="flip-label" style="color:<?php echo $flipbox_color_scheme;?>;"><?php echo $flipbox_label;?></div>
		    </div>
		</div><!-- END .front -->
		
		<div class="face back" style="color: rgb(255, 255, 255); background:<?php echo $flipbox_color_scheme;?>; border-style: dashed; border-width: 4px; border-color:<?php echo $flipbox_color_scheme;?>;">
			<div class="ifb-flip-box-section ">
			<div class="ifb-desc-back">
			<div class="ifb-flip-box-section-content">
			<?php echo "<div class='default-desc'><p>$flipbox_truncate</p></div>";?>
			<div class="flip_link">
			  <p><a target="_blank" href="<?php  echo esc_url($flipbox_url);?>"><strong><?php echo $read_more_link?></strong></a></p>
			</div>
			</div>
			</div>
			</div>
		</div>
		<?php
		echo'</div>';
		echo'</div>';
	} 
		
	else if($flip_layout=="with-image")
	{?>
			<div class="face front">
				<div class="ifb-flip-box-section-with-image">
					<div class="inner-with-image">   
					<?php
					if(!empty($flipbox_image)){
					echo'<img  src="'.$flipbox_image.'">';
					}
					else 
					{
					?><img class="img-thumbnail" src="<?php echo CFB_IMAGE_DIR ;?>/black-background.jpg"><?php ;
					}
					?>  
					</div>
				</div>
			</div> 
			<div class="face back" style="color: rgb(255, 255, 255); background:<?php echo $flipbox_color_scheme;?>;"> 
				<div class="ifb-flip-box-section-with-image ">
					<div class="inner text-center"> 
					<?php   
					echo "<div class='image-title'><p style='color:rgb(255, 255, 255);'><strong>$flipbox_title</strong></p></div>";
					echo "<div class='image-label'><p>$flipbox_label</p></div>";
					echo "<div class='image-desc'><p>$flipbox_truncate</p></div>";
					?>
					<div class="flip_link">
					<p><a target="_blank" href="<?php  echo esc_url($flipbox_url);?>"><strong><?php echo $read_more_link?></strong></a></p>
					</div>
					</div>
				</div>
			</div>
			<?php 
		echo'</div>';	 
		echo'</div>';
	}
		 
	else if ($flip_layout=="solid-with-icon")

	{?>
		<div class="face front" style="color: rgb(51, 51, 51); background:white; border-style: solid; border-width: 1px; border-color:<?php echo "$flipbox_color_scheme";?>;"> 
		   <div class="ifb-flip-box-section">
			  <div class="flip-box-icon">
			  <div class="ult-just-icon-wrapper  ">
			  <div class="align-icon" style="text-align:center;">
			  <div class="aio-icon none " style="color:<?php echo "$flipbox_color_scheme";?>;display:inline-block;">
				<i class="fa <?php echo "$flipbox_icon";?>" style="font-size:<?php echo $icon_size?>!important;">
				</i>
			  </div>
			  </div>
			  </div>
			  </div>
			  
			  <div class="flip-title" style="color:<?php echo $flipbox_color_scheme;?>;"><strong><?php echo $flipbox_title;?></strong></div>
			  <div class="flip-desc" style="color:<?php echo $flipbox_color_scheme;?>;"><?php   echo $flipbox_label;?></div>
			</div>
		</div> <!-- END .front -->
		
		<div class="face back" style="background:white; border-style: solid; border-width: 1px; border-color:<?php echo $flipbox_color_scheme;?>;"> 
		    <div class="ifb-flip-box-section ">
			<div class="ifb-desc-back">
			<div class="ifb-flip-box-section-content ult-responsive">
			  <?php
			   echo "<div class='with-icon-desc'><p style='color:$flipbox_color_scheme'>$flipbox_truncate</p></div>";
			  ?>
			   <div class="flip_link">
				<p><a target="_blank"  style="color:<?php echo "$flipbox_color_scheme";?>" href="<?php  echo esc_url($flipbox_url);?>"><strong><?php echo $read_more_link?></strong></a></p>
			   </div>
			</div>
			</div>
		    </div>
		</div><!-- END .back -->
			<?php
		echo'</div>'; 
		echo'</div>';  	  
	}
echo'</div>';
 ?>