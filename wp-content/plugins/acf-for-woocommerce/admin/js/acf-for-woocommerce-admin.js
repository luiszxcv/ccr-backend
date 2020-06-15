var $ = jQuery;
var class_prefix = '';
var elements_config;
var home_url = '';

$(document).ready(apply_fields_handler);
function update_global_value(config, CATS_CLASS_PREFIX, api_url){
	elements_config = config;
	class_prefix = CATS_CLASS_PREFIX;
	home_url = api_url;
}

function apply_fields_handler() {
	if(!elements_config) return;
	handleRadio();
	handleSelectbox();
	handleCheckbox();
	handleSlideRange();
	handleFileUpload();
	handleImageUpload();
	handleAdditionalCharge();
	handleRepeater();
	handleConditionalLogic();
	handleVariation();
	handleTextArea();
	initFormForFileInput();
}

function initFormForFileInput() {
	$(".cp-acf-fw-form-groups").closest("form").attr('enctype', 'multipart/form-data')
}
function handleTextArea() {
	$(`.${class_prefix}-text-area`).change(function (e) {
		$(e.target).closest(`.${class_prefix}-element`).find(`.${class_prefix}-field-value-input`).val(e.target.value).trigger('change');
	})
}

function handleRadio() {
	$(`.${class_prefix}-radio-option`).click(function (e) {
		$(e.target).closest(`.${class_prefix}-element`).find(`.${class_prefix}-field-value-input`).val(e.target.value).trigger('change');
	})
}
function handleSelectbox() {
	$(`.${class_prefix}-select`).change(function (e) {
		$(e.target).closest(`.${class_prefix}-element`).find(`.${class_prefix}-field-value-input`).val(e.target.value).trigger('change');
	}).trigger('change');
}
function handleVariation() {
	$('.variations').on('change', function (e) {
		$('input[name=variation_id]').trigger('change');
	})
	$('input[name=variation_id]').on('change', function (e) {
		let variation_id = e.target.value;
		$('.acf-fw-variation-data').each(function (index) {
			let str_logic = this.value.replace('wc_variation_id', variation_id);
			if(eval(str_logic)){
				$(this).closest('.cp-acf-fw-form-group').show()
			}else{
				$(this).closest('.cp-acf-fw-form-group').hide()
			}
		});
	}).trigger('change');
}
function handleCheckbox() {
	$(`.${class_prefix}-checkbox-option`).click(function (e) {
		let input_val = [];
		$(e.target).closest(`.${class_prefix}-element`).find(`.${class_prefix}-checkbox-option`).each(function (index) {
			if(this.checked) input_val.push($(this).val());
		})
		$(e.target).closest(`.${class_prefix}-element`).find(`.${class_prefix}-field-value-input`).val(input_val.join(', '));
	})
}

function handleSlideRange() {
	$(`.${class_prefix}-slide-input`).on('change', function (e) {
		$(e.target).parent().find('input[type=number]').val(e.target.value)
	})
	$(`.${class_prefix}-slide-number-input`).on('change', function (e) {
		$(e.target).parent().find('input[type=range]').val(e.target.value)
	})
}

function handleFileUpload() {
	$(`.${class_prefix}-remove-file-btn`).click(function (e) {
		let $element = $(e.target).closest(`.${class_prefix}-element`);
		$element.find(`.${class_prefix}-file-url`).attr('href', '').text('');
		$element.find(`.${class_prefix}-field-value-input`).val('');
		$element.find(`.${class_prefix}-remove-file-btn`).hide();
		$element.find(`.${class_prefix}-upload-file-input`).show();
	})

	$(`.${class_prefix}-upload-file-input`).change(function (e) {
		let $form = $(this).closest("form").clone().empty().append($(this).clone()[0]);
		let formData = new FormData($form[0]);
		let $element = $(this).closest(`.${class_prefix}-element`);
		$(this).hide();
		$element.find('.fa-spinner').show();
		$element.find(".error-msg").text('').hide();
		$(e.target).closest("form").find("button[type=submit]").attr('disabled', true);
		$.ajax({
			url: `${home_url}/?rest_route=/acf_fw_form/api/v1/files`,
			type: 'POST',
			data: formData,
			async: true,
			success: (data) =>  {
				if(data.error){
					$(this).show();
					$element.find(".error-msg").text(data.error).show();
				}else{
					let filename = data.url.split('/').pop()
					$element.find(`.${class_prefix}-file-url`).attr('href', data.url).text(filename);
					$element.find(`.${class_prefix}-field-value-input`).val(data.url);
					$element.find(`.${class_prefix}-remove-file-btn`).show();
				}
				$element.find('.fa-spinner').hide();
				$(e.target).closest("form").find("button[type=submit]").attr('disabled', false);
			},
			error: (error)=>{
				$element.find('.fa-spinner').hide();
				$(e.target).closest("form").find("button[type=submit]").attr('disabled', false);
			},
			cache: false,
			contentType: false,
			processData: false
		});
		return false;
	})
}

function handleImageUpload() {
	$(`.${class_prefix}-upload-img-input`).change(function(e) {
		let $form = $(this).closest("form").clone().empty().append($(this).clone()[0]);
		let formData = new FormData($form[0]);
		let $element = $(this).closest(`.${class_prefix}-element`);
		$(this).hide();
		$element.find('.fa-spinner').show();
		$element.find(".error-msg").text('').hide();
		$(e.target).closest("form").find("button[type=submit]").attr('disabled', true);
		$.ajax({
			url: `${home_url}/?rest_route=/acf_fw_form/api/v1/files`,
			type: 'POST',
			data: formData,
			async: true,
			success: (data) =>  {
				if(data.error){
					$(this).show();
					$element.find(".error-msg").text(data.error).show();
				}else{
					$element.find(`.${class_prefix}-img`).attr('src', data.url);
					$element.find(`.${class_prefix}-field-value-input`).val(data.url);
					$element.find(`.${class_prefix}-img-container`).show();
				}
				$element.find('.fa-spinner').hide();
				$(e.target).closest("form").find("button[type=submit]").attr('disabled', false);
			},
			error: (error)=>{
				$element.find('.fa-spinner').hide();
				$(e.target).closest("form").find("button[type=submit]").attr('disabled', false);
			},
			cache: false,
			contentType: false,
			processData: false
		});
		return true;
	});
	$(`.${class_prefix}-remove-img-btn`).click(function (e) {
		let $element = $(e.target).closest(`.${class_prefix}-element`);
		$element.find(`.${class_prefix}-img`).attr('src', '');
		$element.find(`.${class_prefix}-field-value-input`).val('');
		$element.find(`.${class_prefix}-upload-img-input`).val('').show();
	})
}

function handleRepeater() {
	//avoid redundant inputs when post
	$(`${class_prefix}-repeater-template :input`).attr('disabled', true);
	//hover to show remove button
	$(`.${class_prefix}-repeater-block`).hover(function (e) {
			$(e.target).closest(`.${class_prefix}-repeater-block`).find(`.${class_prefix}-remove-repeater-btn`).show();
			$(e.target).closest(`.${class_prefix}-repeater-block`).css('background-color', '#dfeaed');
		},
		function (e) {
			$(e.target).closest(`.${class_prefix}-repeater-block`).find(`.${class_prefix}-remove-repeater-btn`).hide();
			$(e.target).closest(`.${class_prefix}-repeater-block`).css('background-color', '#cedade');
		});
	//
	$(`.${class_prefix}-remove-repeater-btn`).click(function (e) {
		$(e.target).closest(`.${class_prefix}-repeater-block`).remove();
	});

	$(`.${class_prefix}-repeater-add-btn`).click( function (e) {
		e.preventDefault();
		let $thisRepeater = $(e.target).closest(`.${class_prefix}-repeater-wrap`);
		let $clonedBlock = $thisRepeater.find(`.${class_prefix}-repeater-template`)
			.clone(true).removeClass(`${class_prefix}-repeater-template`).removeAttr("style");
		let newBlockID = Date.now();
		$clonedBlock.find(`.${class_prefix}-element`).each(function () {
			this.id = this.id.replace('template', newBlockID);
			$(this).find(":input").each(function () {
				this.disabled = false;
				this.name = this.name.replace('template', newBlockID);
				this.id = this.id.replace('template', newBlockID);
			})
			$(this).find("label").each(function () {
				$(this).attr('for', $(this).attr('for').replace('template', newBlockID));
			})
		});
		$clonedBlock.appendTo($thisRepeater.find(`.${class_prefix}-repeater-blocks`));
	});
}


function handleAdditionalCharge() {
	elements_config.forEach((elem_config)=>{
		if(elem_config.pricing_settings){
			$(`#${elem_config.field_name}`).change(function (e) {
				let str_val;
				if(e.target.type == 'checkbox'){
					str_val = $(e.target).closest(`.${class_prefix}-element`).find(`.${class_prefix}-field-value-input`).val();
				}else{
					str_val = e.target.value;
				}
				update_total_additional_charge_input(elem_config.name_input, calculate_field_additional_charge( str_val, elem_config.pricing_settings));
			})
			// update at start up
			update_total_additional_charge_input(elem_config.name_input, calculate_field_additional_charge( elem_config.value, elem_config.pricing_settings));
		}
	});
}

function calculate_field_additional_charge(str_value, pricing_settings) {
	let price = 0;
	pricing_settings.forEach((setting) => {
		if(str_value.includes(setting.value)) price += parseInt(setting.price);
	});
	return price;
}

function update_total_additional_charge_input(name_input, field_calculated_value) {
	let $total_additional_charge_input = $(`#${class_prefix}-total-additional-charge-map-input`);
	let value_hash = JSON.parse($total_additional_charge_input.val());
	value_hash[`${name_input}`] = field_calculated_value;
	$total_additional_charge_input.val( JSON.stringify(value_hash) );

	//update total input
	total = Object.values(value_hash).reduce((a,b)=> a+b);
	$(`#${class_prefix}-total-additional-charge-input`).val(total);

	//update display
	$(`#${class_prefix}-total-additional-charge-span`).html(total);
	if (total>0)
		$(`.${class_prefix}-total-additional-charge-wrap`).show();
	else
		$(`.${class_prefix}-total-additional-charge-wrap`).hide();
}


function handleConditionalLogic() {
	elements_config.forEach((config)=>{
		if(listOfDependentFields(config.conditional_settings) != ''){
			let $form = $(`#${config.field_name}`).closest(`.${class_prefix}-form-group`);
			$form.on('change', function(){
				eval(processCondition(config.conditional_settings)) ? $(`#${config.field_name}`).show() : $(`#${config.field_name}`).hide();
			}.bind($form)).trigger('change');
		}
	});
}

function processCondition(conditions) {
	var string = '';
	if (conditions.hasOwnProperty('or') || conditions.hasOwnProperty('and')) {
		var key = Object.keys(conditions)[0];
		var children = conditions[key].map((condition) => processCondition(condition));
		string = `(${children.join(` ${String(key) === 'or' ? '||' : '&&'} `)})`;
	} else {
		if (conditions.target == '') {
			string = 'true';
		} else {
			switch (conditions.condition) {
				case '!=empty':
					string = `this.find('#${conditions.target}').val() != ''`;
					break;
				case '==empty':
					string = `this.find('#${conditions.target}').val() == ''`;
					break;
				case '==':
					string = `this.find('#${conditions.target}').val() == '${conditions.value}'`;
					break;
				case '!=':
					string = `this.find('#${conditions.target}').val() != '${conditions.value}'`;
					break;
				case '==pattern':
					string = `this.find('#${conditions.target}').val().match('${conditions.value}')`;
					break;
				case '==contains':
					string = `this.find('#${conditions.target}').val().includes('${conditions.value}')`;
					break;
			}
		}
	}
	return string;
}

function listOfDependentFields(conditions) {
	if (conditions.hasOwnProperty('or') || conditions.hasOwnProperty('and')) {
		var key = Object.keys(conditions)[0];
		var target = conditions[key].map((condition) => this.listOfDependentFields(condition)).flat();
	} else {
		if (conditions.target == '') {
			target = '';
		} else {
			target = `#${conditions.target}`;
		}
	}
	return target;
}

