<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'patient-adv-search-form',
	'enableAjaxValidation'=>true,
	'focus'=>'#Patient_hos_num',
	//'action' => Yii::app()->createUrl('patient/results')
));?>
	<div id="search_patient_details" class="form_greyBox clearfix">
		<h3>Or, search by person details</h3>
		<div class="form_column">
			<div class="inputLayout clearfix">
				<?php echo CHtml::label('First name:<span class="labelRequired">First name is required</span>', 'first_name');?>
				<?php echo CHtml::textField('Patient[first_name]', '', array('style'=>'width: 150px;', 'class' => 'topPaddingSmall'));?>
			</div>
			<div class="inputLayout clearfix">
				<?php echo CHtml::label('Last name:<span class="labelRequired">Last name is required</span>', 'last_name');?>
				<?php echo CHtml::textField('Patient[last_name]', '', array('style'=>'width: 150px;', 'class' => 'topPadding'));?>
			</div>
			<div class="multiInputRight clearfix">
				<?php echo CHtml::label('Date of birth:<span class="labelHint">dd / mm / yyyy</span>', 'dob');?>
				<?php echo CHtml::textField('dob_day', '', array('size'=>2, 'maxlength'=>2));?><strong style="margin:0 5px 0 8px;">&#47;</strong>
				<?php echo CHtml::textField('dob_month', '', array('size'=>2, 'maxlength'=>2));?><strong style="margin:0 5px 0 8px;">&#47;</strong>
				<?php echo CHtml::textField('dob_year', '', array('size'=>4, 'maxlength'=>4));?>
			</div>
		</div>
		<div class="form_column">
			<div class="inputLayout clearfix">
				<?php echo CHtml::label('NHS #:<span class="labelHint">for example: 111-222-3333</span>', 'nhs_number')?>
				<?php echo CHtml::textField('Patient[nhs_num]', '')?>
			</div>
			<div class="customRight clearfix">
				<?php echo CHtml::label('Gender:<span class="labelHint">if known</span>', 'gender', array('style'=>'float: left;'));?>
				<?php echo CHtml::radioButtonList('Patient[gender]', '', array('M'=>'Male','F'=>'Female'), array('separator'=>' &nbsp; '))?>
			</div>
		</div>
		<div class="form_button">
			<button type="submit" value="submit" class="btn_findPatient ir" id="findPatient_details">Find patient</button>
		</div>
	</div>
	<?php $this->endWidget();?>
</form>
<script type="text/javascript">
	$('#dob_day').watermark('DD');
	$('#dob_month').watermark('MM');
	$('#dob_year').watermark('YYYY');
</script>
