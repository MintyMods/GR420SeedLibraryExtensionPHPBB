const debug = MINTY_SEEDS.DEBUG;
const expire = 3000;
const labelPosition = "left";
const labelWidth = 120;
const WINDOW_WIDTH = 600; // @todo support moblile / reactive layout
const GRID_SPLIT_AT = 2; 
const GRID_SELECT_URL = "GRID_SELECT_RECORDS";
const GRID_DELETE_URL = "GRID_DELETE_RECORD";
const SEED_POST_URL = "minty_sl_seeds";
const SEED_UPLOAD_URL = "upload/seed_upload";
const SEED_FILES_URL = "upload/list_files";
const BREEDER_POST_URL = "minty_sl_breeder";
const BREEDER_UPLOAD_URL = "upload/breeder_upload";
const BREEDER_SELECT_URL = "BREEDER_SELECT_RECORD";
const SEED_ID = "seed_id";
const BREEDER_ID = "breeder_id";
const IMAGE_UPLOAD = "image_upload";
const GENETICS = "minty_sl_genetics";
const PARENTS = "minty_sl_parents";
const SMELLS = "minty_sl_smells";
const EFFECTS = "minty_sl_effects";
const TASTES = "minty_sl_tastes";
const METATAGS = "minty_sl_meta_tags";
const AWARDS = "minty_sl_awards";
const COMBO_CONTROLS = [PARENTS, SMELLS, EFFECTS, TASTES, METATAGS, AWARDS];
const flowering_type_options = {cols:[{ type:"radioButton", text:"Auto", value:"A"},{ type:"radioButton", text:"Photo", value:"P" }]};
const sex_options = {cols:[{ type:"radioButton", text:"Regular", value:"R"},{ type:"radioButton", text:"Female", value:"F" }]};
const indoor_outdoor_options = {cols: [{ id:"indoor_yn", type:"checkbox", text:"Indoor"},{ id:"outdoor_yn", type:"checkbox", text:"Outdoor"}]};
const month_options = [
  { value: "", content: "Select Month" },
  { value: "jan", content: "January" },
  { value: "feb", content: "February" },
  { value: "mar", content: "March" },
  { value: "apr", content: "April" },
  { value: "may", content: "May" },
  { value: "jun", content: "June" },
  { value: "jul", content: "July" },
  { value: "aug", content: "Augest" },
  { value: "sep", content: "September" },
  { value: "oct", content: "October" },
  { value: "nov", content: "November" },
  { value: "dec", content: "December" }
];
var seedGrid, seedForm, seedWindow, breederForm, breederWindow;
var focusedControl = null;
var fullScreenWindow = false;

function initMintySeedLibData() {
  buildSeedGrid();
  buildSeedButtons();
  buildSeedGridContextMenu();
  buildSeedForm(); 
  buildSeedWindow();
  buildBreederForm();
  buildBreederWindow();
}

function getUploadedFilesList(form) {
  let seed_id = form.getItem(SEED_ID) ? form.getItem(SEED_ID).getValue() : null;
  let breeder_id = form.getItem(BREEDER_ID) ? form.getItem(BREEDER_ID).getValue() : null;
  let url = SEED_FILES_URL + "?" + SEED_ID + "=" + seed_id + 
           "&" + BREEDER_ID + "=" + breeder_id;
  let widget = form.getItem(IMAGE_UPLOAD);
  widget.data.load(url).then(function(items){
    dhx.awaitRedraw().then(function(){
      buildImageViewer(items, widget);
    }.bind(items, widget));
  }.bind(widget));
}

function buildImageViewer(items, widget) {
  let spans = $(".dhx_simplevault-files__item-name");
  spans.each(function(index, span){
    $(span).on('click', function() { 
      new PhotoViewer(items, { index }); 
    }.bind(index));
    $(span).addClass('minty_image_viewer_link');
  });
}

function buildUploadEvents(form) {
  let widget = form.getItem(IMAGE_UPLOAD);
  widget.data.events.on("change",function(id, mode, upload, event){
    if (upload) {
      if (upload.status == "queue" && mode == 'add') {
          window.setTimeout(function(){widget.send()},0);
      } else if (upload.status == 'uploaded') {
        if (mode == 'add') {
          widget.data.forEach(function(file){
            if (file.status == "queue") {
              window.setTimeout(function(){widget.send()},0);        }
          });
        } else if (mode == 'update') {
          dhx.awaitRedraw().then(function(){showUploadProgress(upload)});
        } else if (mode == 'remove') {
          msg('Removed : ' + upload.file.name);
        } 
      } else if (upload.status == 'inprogress') {
        dhx.awaitRedraw().then(function(){showUploadProgress(upload)});
      } else if (upload.status == 'failed') {
        err('Upload Failed : ' + upload.file.name);
      }
    }
    return false;
  });
}

function showUploadProgress(upload) {
  if (upload) {
    let item = $('div[dhx_id="' + upload.id + '"]');
    if (upload.status == "inprogress") {
      item.parent().addClass('minty_file_uploading');
      item.prev().text("Uploading " + Math.round(upload.progress * 100) + "% " + upload.file.name);
    } else if (upload.status == "failed") {
      item.parent().addClass('minty_file_uploading_failed');
      item.prev().text('Failed to Upload ' + upload.file.name);
    } else {
      item.parent().removeClass('minty_file_uploading');
      item.prev().text(upload.file.name);
    }
  }
}

function showAddBreederWindow() {
  getUploadedFilesList(breederForm);
  breederWindow.show();
  breederForm.setFocus("breeder_name");
}

function buildBreederWindow() {
  breederWindow = new dhx.Window({ width: WINDOW_WIDTH, height: 500, title: "Add New Breeder", modal: true, resizable: true, movable: true, closable: false, header: true, footer: true, });
  breederWindow.footer.data.add([
    { type: "spacer" },
    { id: "breeder_cancel", type: "button", value: "Cancel", view: "flat", color: "secondary", icon: "dxi dxi-close-circle", circle:true, padding: "0px 5px", },
    { hidden: Boolean(!canDeleteBreederRecords()), id: "breeder_delete", type: "button", value: "Delete", view: "flat", color: "danger", icon: "dxi dxi-delete", circle:true, padding: "0px 5px", },
    { hidden: Boolean(!(canAddBreederRecords()||canEditBreederRecords())), id: "breeder_save", type: "button", value: "Save", view: "flat", color: "primary", icon: "dxi dxi-checkbox-marked-circle", circle:true, padding: "0px 5px", },
  ], 0);

  breederWindow.footer.events.on("click", function (id) {
    switch (id) {
      case 'breeder_cancel': {
        breederWindowClose()
        break;
      }
      case 'breeder_save': {
        if (breederForm.validate()) {
          saveBreederForm();
        } else {
          breederForm.setFocus("breeder_name");
          err("Breeder Form Validation Failed");
        }
        break;
      }
    }
  });
  breederWindow.attach(breederForm);
}

function saveBreederForm() {
  breederForm.send(BREEDER_POST_URL, "POST", true).then(breederFormSaved).catch(function (e) {
    err(e.statusText);
  });  
}

function buildBreederForm() {
  breederForm = new dhx.Form("minty_breeder_form", {
    rows: [
      { id: BREEDER_ID, type: "input", hidden: true, },
      { id: "upload_id", type: "input", hidden: true},
      { id: "breeder_name", type: "input", validation: validateBreeder, required: true, label: "Name", labelPosition, labelWidth, errorMessage: "Breeder name is a required field",  },
      { id: "breeder_desc", type: "textarea", label: "Description", labelPosition, labelWidth, },
      { id: "breeder_url", type: "input", label: "URL", labelPosition, labelWidth, },
      { id: IMAGE_UPLOAD, mode:"grid", type: "simpleVault", target: BREEDER_UPLOAD_URL, fieldName: "upload", label: "Images", labelInline: true, labelPosition, labelWidth, },
      { id: "sponsor_yn", type: "checkbox", label: "Sponsor",labelInline: true, labelPosition, labelWidth, },
    ]
  })
  capitalizeControlValue(breederForm, 'breeder_name');
  buildBreederFormEvents();
}

function buildBreederFormEvents() {
  breederForm.events.on("BeforeSend", function() {
    parseBreederFormServerReady();
  }); 
  buildUploadEvents(breederForm);
}

function getParsedUploads(form) {
  let parsed = [];
  let uploads = form.getItem(IMAGE_UPLOAD).getValue();
  uploads.forEach(function(upload){
    parsed.push( upload.id );
  });
  return parsed;
}

function parseBreederFormServerReady() {
  breederForm.setValue({"upload_id" : getParsedUploads(breederForm) });
}

function breederFormSaved(response) {
  var json = parseJSON(response);
  if (json && json.saved) {
    seedForm.setValue(BREEDER_ID, json.data.breeder_name);
    loadComboSuggestions(BREEDER_ID, seedForm);
    breederWindowClose();
    msg("'" + json.data.breeder_name + "' Breeder Details Saved");
  } else {
    err("Failed to save Breeder Details!", response);
  }
}

function clearForm(form) {
  form.clear();
  form.getItem(IMAGE_UPLOAD).data.removeAll();
  form.getItem(IMAGE_UPLOAD).clearValidate();
  form.getItem(IMAGE_UPLOAD).clear();
}

function validateBreeder(value) {
  var data = seedForm.getItem(BREEDER_ID).combobox.data;
  var control = breederForm.getItem("breeder_name");
  if (value == "") {
    control.config.errorMessage = "Breeder Name is a required field!";
    return false;
  } else {
    data.forEach(function (option) {
      if (clean(option.value) == clean(value)) {
        control.config.errorMessage = "Breeder '" + option.value + "' already exists!";
        return false;
      }
    });
  }
  return true;
}

function buildSeedButtons() {
  var buttons = new dhx.Form("minty_buttons", { 
    css:'minty_buttons',
    rows: [{
      cols: [
        { name: "info_button", disabled:!MINTY_SEEDS.DEBUG, type: "button", text: MINTY_SEEDS.GRID_TITLE, view: "link", color: "secondary", circle:true, css:'minty_grid_title'},
        { type: "spacer" },
        { hidden: Boolean(!canAddRecords()), name: "add_button", type: "button", text: "New", view: "flat", color: "primary", icon: "dxi dxi-plus-circle", circle:true, padding: "0px 5px", },
        { hidden : Boolean(!canEditRecords()), name: "edit_button", type: "button", text: "Edit", view: "flat", color: "primary", icon: "dxi dxi-pencil", circle:true, padding: "0px 5px",},
        { hidden : Boolean(!canDeleteRecords()), name: "delete_button", type: "button", text: "Delete", view: "flat",  color: "danger", icon: "dxi dxi-delete", circle:true, padding: "0px 5px",},
        { hidden : Boolean(!canReadRecords()), name: "refresh_button", type: "button", text: "Refresh", view: "flat",  color: "primary", icon: "dxi dxi-rotate-right", circle:true, padding: "0px 5px",},
        { hidden : Boolean(!canReadRecords()), name: "view_button", type: "button", text: "View", view: "flat",  color: "primary", icon: "dxi dxi-eye", circle:true, padding: "0px 5px",},
        { hidden : Boolean(!canReadRecords()), name: "search_button", type: "button", text: "Search", view: "flat",  color: "primary", icon: "dxi dxi-magnify", circle:true, padding: "0px 5px",},
      ]  
    }]
  });
  buttons.getItem("info_button").events.on("Click", function (events) {
    showAbout();
  });
  buttons.getItem("refresh_button").events.on("Click", function (events) {
    reloadSeedGridRows();
  });
  buttons.getItem("view_button").events.on("Click", function (events) {
    viewSeedGridRecord(getSelectedGridRow());
  });
  buttons.getItem("search_button").events.on("Click", function (events) {
    searchSeedGridRecord();
  });
  buttons.getItem("add_button").events.on("Click", function (events) {
    addNewSeedGridRecord();
  });
  buttons.getItem("edit_button").events.on("Click", function (events) {
    editSeedGridRecord(getSelectedGridRow());
  });
  buttons.getItem("delete_button").events.on("Click", function (events) {
    deleteSeedGridRecord(getSelectedGridRow());
  });
}

function buildSeedGrid() {
  seedGrid = new dhx.Grid("minty_seed_grid", {
    columns: [
      { width: 0, id: "id", hidden: true, header: [{ text: "ID" }] },
      { width: 0, id: BREEDER_ID, hidden: true, header: [{ text: "Breeder ID" }] },
      { width: 150, id: "breeder_name", header: [{ text: "Breeder" }], type: "string"},
      { width: 150, id: "seed_name", type: "string", header: [{ text: "Name" }], type: "string" },
      { width: 120, id: PARENTS, template: gridDisplayComboValueAsTag, header: [{ text: "Parents" }], type: "string"},
      { width: 40, id: "flowering_type", header: [{ text: "Type" }], type: "string" },
      { width: 40, id: "sex", header: [{ text: "Sex" }], type: "string" },
      { width: 50, id: "indoor_yn", type: "boolean", header: [{ text: "Indoor" }] },
      { width: 70, id: "outdoor_yn", type: "boolean", header: [{ text: "Outdoor" }] },
      { width: 110, id: "flowering_time", header: [{ text: "Flowering Time" }], type: "string"},
      { width: 110, id: "height_indoors", type: "string", header: [{ text: "Indoor Height" } ], type: "string" },
      { width: 110, id: "yeild_indoors", type: "string", header: [{ text: "Indoor Yeild" }], type: "string" },
      { width: 110, id: "height_outdoors", type: "string", header: [{ text: "Outdoor Height" }], type: "string" },
      { width: 110, id: "yeild_outdoors", type: "string", header: [{ text: "Outdoor Yeild" }], type: "string" },
      { width: 110, id: "harvest_month", type: "date", dateFormat: "%M", header: [{ text: "Harvest Month" }], type: "string"},
      { width: 200, id: "seed_desc", header: [{ text: "Desc" }] },
      { width: 0, id: "forum_url", hidden: true,  header: [{ text: "Source url" }] },
      { width: 110, id: "thc", type: "string", header: [{ text: "THC" }], type: "string" },
      { width: 110, id: "cbd", type: "string", header: [{ text: "CBD" }], type: "string" },
      { width: 110, id: "indica", type: "string", header: [{ text: "Indica" }], type: "string" },
      { width: 110, id: "sativa", type: "string", header: [{ text: "Sativa" }], type: "string" },
      { width: 110, id: "ruderalis", type: "string", header: [{ text: "Ruderalis" }], type: "string" },
      { width: 120, id: GENETICS, template: gridDisplayComboValueAsTag, header: [{ text: "Genetics" }], type: "string"},
      { width: 120, id: SMELLS, template: gridDisplayComboValueAsTag, header: [{ text: "Smells" }], type: "string" },
      { width: 120, id: TASTES, template: gridDisplayComboValueAsTag, header: [{ text: "Tastes" }], type: "string" },
      { width: 120, id: EFFECTS, template: gridDisplayComboValueAsTag, header: [{ text: "Effects" }], type: "string" },
      { width: 120, id: AWARDS, template: gridDisplayComboValueAsTag, header: [{ text: "Awards" }], type: "string" },
      { width: 120, id: METATAGS, template: gridDisplayComboValueAsTag, header: [{ text: "Effects" }], type: "string" },
    ],
    leftSplit: MINTY_SEEDS.SPLIT_ENABLED ? GRID_SPLIT_AT : false, 
    editable: false,
    autoEmptyRow: false,
    height: 520,
    multiselection: false,
    selection: "row",
    sortable: false,
    resizable: true,
    autoWidth: true,
    autoHeight: false,
    htmlEnable: true,
  });
  buildGridDoubleClickAction();
  seedGrid.data.load(new dhx.LazyDataProxy(GRID_SELECT_URL, { limit: 15, prepare: 0, delay: 10, from: 0 }));
}

function gridDisplayComboValueAsTag(tags, row, col) {
  let control = seedForm.getItem(col.id);
  let widget = control.getWidget();
  let result = '<ul class="minty_combo_list">';
  if (tags) {
    tags.forEach(function(tag){
      if (widget.data.getItem(tag)) {
        result = result + '<li>'+widget.data.getItem(tag).value + '</li>';
      }
    });
  }
  return result + '</ul>';
}

function buildGridDoubleClickAction() {
  seedGrid.events.on("cellDblClick", function (row, column, e) {
    e.preventDefault();
    if (canEditRecords()) {
      editSeedGridRecord(row);
    } else {
      viewSeedGridRecord(row);
    }
  });
}

function searchSeedGridRecord() {
  err('Searching has not been implemented yet...');
}

function addNewSeedGridRecord() {
  seedForm.enable();
  clearForm(seedForm);
  showSeedWindow();
  dhx.awaitRedraw().then(function () { seedForm.setFocus(BREEDER_ID); });
}

function viewSeedGridRecord(row) {
  if (row) {
    showSeedGridRecord(row, true);
  } else {
    err('First select a grid row to view,<br/> you can also double click<br/> or right click a row to view');
  }
}

function editSeedGridRecord(row) {
  if (row) {
    showSeedGridRecord(row, false);
  } else {
    err('First select a grid row to edit');
  }
}

function showSeedGridRecord(row, readonly) {
  let parsed = {
    seed_id: row.id,
    harvest_month: parseHarvestMonth(row.harvest_month),
    indoor_outdoor: {
      indoor_yn : row.indoor_yn,
      outdoor_yn : row.outdoor_yn,
    }
  }
  seedForm.setValue(Object.assign(row, parsed));
  readonly ? seedForm.disable() : seedForm.enable();
  dhx.awaitRedraw().then(function () {
    showSeedWindow();
  });
}

function parseComboValuesForSeedFormDisplay(row, parsed) {
  COMBO_CONTROLS.forEach(function (name) {
    let control = seedForm.getItem(name);
    let combo = control.getWidget();
    let options = this[name.slice(0, -1)];
    if (options && options.length > 0) {
      let value = [];
      options.forEach(function(option){
        value.push(getComboOptionId(combo,option));
      });
      parsed = Object.assign(parsed, {[name] : value})
    }
  }.bind(row)); 
  return parsed; 
}

function getComboOptionId(combo, option) {
  let result = null;
  combo.data.forEach(function(entry){
    result = combo.data.getIndex(entry.id);
  });
  return result;
}

function addComboEvents(combobox) {
  combobox.events.on("Input", function (value) {
    this._input_value = value;
  });
  combobox.events.on("BeforeClose", function () {
    var value = this._input_value;
    if (value && !this.getValue(true).includes(value)) {
      value = value.capitalize();
      tag = 'TAG[' + value +']';
      const id = 'TAG:'+ Math.floor(Math.random() * 10000);
      this.data.add({ id, value, tag }, 0);
      dhx.awaitRedraw().then(function () {
        this.setValue(this.getValue(true).push(id));
        this.paint();
        dhx.awaitRedraw().then(function () { this.focus(); }.bind(this));
      }.bind(this));
    }
    this._input_value = undefined;
  });
}

function parseSeedFormServerReady() {
  seedForm.setValue({"indoor_yn": seedForm.getItem("indoor_outdoor").getValue("indoor_yn")});
  seedForm.setValue({"outdoor_yn": seedForm.getItem("indoor_outdoor").getValue("outdoor_yn")});
  COMBO_CONTROLS.forEach(function(control) {
    parseUserTagForServer(control);
  });
  seedForm.setValue({"upload_id" : getParsedUploads(seedForm) }); 
}

function parseUserTagForServer(control) {
  let data = seedForm.getItem(control).getWidget().getValue(true);
  let parsed = [];
  data.forEach(function(value){ 
    if (value.indexOf('TAG:') > -1) {
      let options = seedForm.getItem(control).getWidget().data;
      parsed.push(options.getItem(value).tag);
    } else {
      parsed.push(value); 
    }
  });
  seedForm._state[control] = parsed;
}

function buildSeedForm() {
  seedForm = new dhx.Form("minty_seed_form", {
    css: "dhx_widget--bordered",
    rows: [
      { name: SEED_ID, type: "text", hidden: true, },
      { name: "indoor_yn", type: "text", hidden: true, },
      { name: "outdoor_yn", type: "text", hidden: true, },
      { cols: [
        { disabled: Boolean(!canAddBreederRecords()), name: BREEDER_ID, filter: fuzzySearch, type: "combo", width: 340, label: "Breeder", required: true, placeholder: "Select Breeder", errorMessage: "You must select a valid breeder from the list", labelWidth, labelPosition,  },
        { hidden: Boolean(!canAddBreederRecords()), name: "add_breeder_button", type: "button", text: "Add New Breeder", size: "medium", width: 160, view: "flat", icon: "dxi dxi-plus", color: "secondary", view: "link", circle:true, padding: "0px 5px",},]
      },
      { name: "seed_name", type: "input", label: "Name", labelPosition, labelWidth, required: true, placeholder: "Enter the full name of the plant", errorMessage: "Plant name is mandatory to save a new record", disabled: Boolean(!canAddRecords()), },
      { name: PARENTS, filter: fuzzySearch, type: "combo", multiselection: true, label: "Parents", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: "flowering_type", type: "radioGroup", options: flowering_type_options, required: true, label: "Type", labelWidth, labelPosition, errorMessage: "Flowering type is a required field", disabled: Boolean(!canAddRecords()), },
      { name: "sex", type: "radioGroup", options: sex_options, required: true, label: "Sex", errorMessage: "Sex is a required field", labelWidth, labelPosition, disabled: Boolean(!canAddRecords()), },
      { name: "indoor_outdoor", type: "checkboxGroup", options: indoor_outdoor_options, label: "Environment", labelWidth, labelPosition, labelInline: true, disabled: Boolean(!canAddRecords()), },
      { name: "seed_desc", type: "textarea", resizable:true, label: "Description", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: "flowering_time", type: "input", label: "Flowering Time", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: "height_indoors", type: "input", label: "Indoor Height", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()),  },
      { name: "yeild_indoors", type: "input", label: "Indoor Yeild", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: "height_outdoors", type: "input", label: "Outdoor Height", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: "yeild_outdoors", type: "input", label: "Outdoor Yeild", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: "harvest_month", type: "select", options: month_options, label: "Harvest Month", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: "thc", type: "input", label: "THC", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: "cbd", type: "input", label: "CBD", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: "indica", type: "input", label: "Indica", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: "sativa", type: "input", label: "Sativa", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: "ruderalis", type: "input", label: "Ruderalis", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: GENETICS, filter: fuzzySearch, type: "combo", multiselection: true, label: "Genetics", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: "forum_url", type: "input", label: "URL", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { id: "upload_id", type: "input", hidden: true},
      { id: IMAGE_UPLOAD, type: "simpleVault", singleRequest: false, target: SEED_UPLOAD_URL, fieldName: "upload", label: "Images", labelInline: true, labelPosition, labelWidth, },
      { name: AWARDS, filter: fuzzySearch, type: "combo", multiselection: true, label: "Awards", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: SMELLS, filter: fuzzySearch, type: "combo", multiselection: true, label: "Smell", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: TASTES, filter: fuzzySearch, type: "combo", multiselection: true, label: "Taste", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: EFFECTS, filter: fuzzySearch, type: "combo", multiselection: true, label: "Effect", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      { name: METATAGS, filter: fuzzySearch, type: "combo", multiselection: true, label: "Tags", labelPosition, labelWidth, disabled: Boolean(!canAddRecords()), },
      
    ]
  });
  loadComboSuggestions(BREEDER_ID, seedForm);
  loadComboSuggestions(GENETICS, seedForm);
  loadComboControlSuggestions(COMBO_CONTROLS, seedForm);
  buildSeedFormEvents();
}

function loadComboSuggestions(name, form) {
  form.getItem(name).getWidget().data.load(name);
  // @todo can we proxy these two combo options...
  //var proxy = new dhx.LazyDataProxy(name, { limit: 15, prepare: 0, delay: 10, from: 0 });
  // form.getItem(name).getWidget().data.load(proxy);
}

function buildSeedFormEvents() {
  seedForm.getItem("add_breeder_button").events.on("Click", showAddBreederWindow);

  seedForm.events.on("AfterValidate", function(name, value, isValid) {
     if (!isValid && !focusedControl) {
        focusedControl = name;
      }
  }); 
  seedForm.events.on("BeforeSend", function() {
    parseSeedFormServerReady();
  }); 

  buildUploadEvents(seedForm);
  capitalizeControlValue(seedForm, 'seed_name'); 
}

function saveSeedFormRecord(callback) {
  if (seedForm.validate()) {
    seedForm.send(SEED_POST_URL, "POST", true).then(function (json) {
        focusedControl = null;  
        result = parseJSON(json);
        msg("'" + result.seed_name + "' Record Saved");
        reloadSeedGridRows();
        if (callback) callback(result);
    }).catch(function(e){
      if (e.status) {
        err("Error Saving: " + e.status + ' : ' + e.statusText, e);
      } else {
        err("Error Saving: " + e);
      }
    });
  } else {
    err("Failed to validate form, correct the issue and try again.");
    seedForm.setFocus(focusedControl ? focusedControl : BREEDER_ID); 
  }
}

function loadComboControlSuggestions(controls, form) {
  controls.forEach(function (name) {
    let control = form.getItem(name);
    let widget = control.getWidget();
    widget.data.load(name);
    addComboEvents(widget);
  });
}

function deleteSeedGridRecord(row) {
  if (row && canDeleteRecords()) {
    dhx.confirm({
      buttons:["cancel", "ok"],
      header: "Permanently Delete Record - Are you sure?",
      text: "Are you sure you want to delete the database entry for '" + row.seed_name + "' by " + row.breeder_name,
    }).then(function (confirmed) {
      if (confirmed) {
        const url = GRID_DELETE_URL + '?seed_id=' + row.id;
        dhx.ajax.get(url).then(function (result) {
          if (!parseJSON(result)) {
            reloadSeedGridRows();
            msg('deleted record id ' + row.id);
          } else {
            err('Failed to delete record id ' + row.id);
          }
        }).catch(function (e) {
          err('Grid Delete Error : ' + e.statusText, e);
        });
      }
    });
  }else {
    err('First select a grid row to delete');
  }
}
  
function reloadSeedGridRows() {
  clearForm(seedForm);
  clearForm(breederForm);
  seedGrid.data.removeAll();
  seedGrid.data.load(new dhx.LazyDataProxy(GRID_SELECT_URL, { limit: 15, prepare: 0, delay: 25, from: 0 }));
  loadComboControlSuggestions(COMBO_CONTROLS, seedForm);
  seedGrid.paint();
  dhx.awaitRedraw().then(function () { seedGrid.scrollTo("0", "seed_name"); });
}

function buildSeedWindow() {
  seedWindow = new dhx.Window({ height: 600, width: WINDOW_WIDTH, title: "Seed Details", modal: true, resizable: true, movable: true, closable: false, header: true, footer: true, });
  buildSeedWindowToolbar();
  buildSeedWindowFooter();
  seedWindow.attach(seedForm);
}

function buildSeedWindowToolbar() {
  seedWindow.header.data.add([
    { type: "spacer" },
    { id: "close", circle:true, icon: "dxi dxi-close" },
    { id: "save", circle:true, icon: "dxi dxi-check", hidden:Boolean(!canAddRecords()) },
    { id: "save_new", circle:true, icon: "dxi dxi-plus", hidden:Boolean(!canAddRecords()) },
    { id: "fullscreen", circle:true, icon: "dxi dxi-arrow-expand" },
  ], 1);
  seedWindow.header.events.on('click', function(id) {
    switch (id) {
      case 'close' :
        seedWindowClose();
        break;
      case 'save' :
        seedFormSave();
        break;
      case 'save_new' :
        seedFormSaveNew();
        break;
      case 'fullscreen' :
        seedWindowToggleFullScreen();
        break;
    }
  });
  seedWindow.events.on("BeforeHide", function(position, events){
    seedForm.enable();
    return true;
  });  
}

function buildSeedGridContextMenu() {
  const seedGridContextMenu = new dhx.ContextMenu(null, { css: "dhx_widget--bg_gray" });
  const contextmenu_data = [
    { "hidden" : Boolean(!canReadRecords()), "id": "grid_row_view", "icon": "dxi dxi-eye", "value": "View" },
    { "hidden" : Boolean(!canReadRecords()), "id": "grid_row_refresh", "icon": "dxi dxi-rotate-right", "value": "Refresh" },
    { "hidden" : Boolean(!canReadRecords()), "id": "grid_row_search", "icon": "dxi dxi-magnify", "value": "Search" },
    { "hidden" : Boolean(!canAddRecords()), "id": "grid_row_add", "icon": "dxi dxi-plus", "value": "New" },
    { "hidden" : Boolean(!canEditRecords()), "id": "grid_row_edit", "icon": "dxi dxi-pencil", "value": "Edit" },
    { "hidden" : Boolean(!canDeleteRecords()), "id": "grid_row_delete", "icon": "dxi dxi-delete", "value": "Delete" },
    { "id": "grid_row_about", "icon": "dxi dxi-help-circle-outline", "value": "About" },
  ];
  seedGridContextMenu.data.parse(contextmenu_data);
  seedGrid.events.on("CellRightClick", function (row, column, e) {
    seedGrid.selection.setCell(row.id);
    seedGridContextMenu.showAt(e);
    e.preventDefault();
  });

  seedGridContextMenu.events.on("Click", function (option, e) {
    var cell = seedGrid.selection.getCell();
    switch (option) {
      case 'grid_row_add':
        addNewSeedGridRecord();
        break;
      case 'grid_row_view':
        viewSeedGridRecord(cell.row);
        break;
      case 'grid_row_refresh':
        reloadSeedGridRows();
        break;
      case 'grid_row_search':
        searchSeedGridRecord();
        break;
      case 'grid_row_edit':
        editSeedGridRecord(cell.row);
        break;
      case 'grid_row_delete':
        deleteSeedGridRecord(cell.row);
        break;
      case 'grid_row_about':
        showAbout();
        break;
    }
  });
}

function buildSeedWindowFooter() {
  seedWindow.footer.data.add([
    { type: "spacer" },
    { id: "cancel_button", type: "button", icon: "dxi dxi-close-circle", size: "medium", color: "secondary", value: "Cancel", circle:true, padding: "0px 5px", },
    { hidden: Boolean(!canDeleteRecords()), id: "delete_button", type: "button", value: "Delete", size: "medium", view: "flat",  color: "danger", icon: "dxi dxi-delete", circle:true, padding: "0px 5px",},
    { hidden: Boolean(!canAddRecords()), id: "save_button", type: "button", icon: "dxi dxi-checkbox-marked-circle", view: "flat", size: "medium", color: "primary", value: "Save", submit: true, circle:true, padding: "0px 5px", },
    { hidden: Boolean(!canAddRecords()), id: "save_new_button", type: "button", value: "Save & New", size: "medium", view: "flat", color: "primary", icon: "dxi dxi-plus-circle", circle:true, padding: "0px 5px", },
  ], 0);
  seedWindow.footer.events.on("click", function (id) {
    if (id === "cancel_button") {
      seedWindowClose();
    } else if (id === "delete_button") {
      deleteSeedGridRecord(getSelectedGridRow());
    } else if (id === "save_new_button") {
      seedFormSaveNew();
    } else if (id === "save_button") {
      seedFormSave();
    }
  });
}

function isRowSelected() {
  return seedGrid.selection && seedGrid.selection.getCell() && seedGrid.selection.getCell().row;
}

function getSelectedGridRow() {
  return isRowSelected() ? seedGrid.selection.getCell().row : null;
}

function showSeedWindow() {
  getUploadedFilesList(seedForm);
  seedWindow.show();
}

function seedFormSave() {
  saveSeedFormRecord(function(){
    seedWindowClose();
  });
}

function seedFormSaveNew() {
  saveSeedFormRecord(function(){
    clearForm(seedForm);
    seedForm.setValue(BREEDER_ID, seedForm.getValue(BREEDER_ID));     
    seedForm.setFocus("seed_name");
  });  
}

function seedWindowClose() {
  clearForm(seedForm);
  seedWindow.hide();
}
function breederWindowClose() {
  clearForm(breederForm);
  breederWindow.hide();
}

function parseHarvestMonth(value) {
  return (value && value != '') ? value.substring(0, 3).toLowerCase() : value;
}

function canReadRecords() {
  return MINTY_SEEDS.ADMIN || MINTY_SEEDS.READ;
}

function canAddRecords() {
  return MINTY_SEEDS.ADMIN || MINTY_SEEDS.ADD;
}

function canEditRecords() {
  return MINTY_SEEDS.ADMIN || MINTY_SEEDS.EDIT;
}

function canDeleteRecords() {
  return MINTY_SEEDS.ADMIN || MINTY_SEEDS.DELETE;
}

function canAddBreederRecords() {
  return MINTY_SEEDS.ADMIN || MINTY_SEEDS.ADD_BREEDER;
}

function canEditBreederRecords() {
  return MINTY_SEEDS.ADMIN || MINTY_SEEDS.EDIT_BREEDER;
}

function canDeleteBreederRecords() {
  return MINTY_SEEDS.ADMIN || MINTY_SEEDS.DELETE_BREEDER;
}

function isEnabledAndActive() {
  return MINTY_SEEDS.USER_ENABLED && (MINTY_SEEDS.READ || MINTY_SEEDS.ADMIN);
}

function showAbout() {
  const html = '<div class="minty_version">Version : ' + MINTY_SEEDS.VERSION + '</div>' + 
              '<div class="minty_url">Source : <a href=' + MINTY_SEEDS.URL + '>GitHub</a></div>' +
              '<div class="minty_version">Debugging : ' + MINTY_SEEDS.DEBUG + '</div>' +
              '<div class="minty_logo"></div>'; 
  const dhxWindow = new dhx.Window({
      width: 340,
      height: 200,
      modal:true,
      title: "Minty Seed Library",
      css: "minty_about",
      html
  });
  dhxWindow.show();
}

function clean(text) {
  return text ? text.trim().toLowerCase() : "";
}

function seedWindowToggleFullScreen() {
  fullScreenWindow = !fullScreenWindow;
  seedWindowDisplayFullScreen(fullScreenWindow);
}

function seedWindowDisplayFullScreen(full) {
  if (full) {
    seedWindow.header.data.update("fullscreen", { icon: "dxi dxi-arrow-collapse" });
    seedWindow.setFullScreen();
  } else {
    seedWindow.header.data.update("fullscreen", { icon: "dxi dxi-arrow-expand" });
    seedWindow.unsetFullScreen();
  }
}

function parseJSON(json) {
  try {
    return JSON.parse(json);
  } catch (e) {
    err("ERROR Parsing JSON: " + e, json);
  }
}

function msg(text, debug) {
  console.log(text,  debug ? debug : '');
  dhx.message({ text, css: "dhx_message--success", icon: "dxi-checkbox-marked-circle", expire });
}

function err(text, debug, extra) {
  console.log(text, debug, extra);
  dhx.message({ text, css: "dhx_message--error", icon: "dxi-close", expire });
}

function fuzzySearch(item, target) {
  var source = item.value.toLowerCase();
  target = target.toLowerCase();
  var sourceLen = source.length;
  var targetLen = target.length;
  if (targetLen > sourceLen) {
    return false;
  }
  var sourceIndex = 0;
  var targetIndex = 0;
  while (sourceIndex < sourceLen && targetIndex < targetLen) {
    if (source[sourceIndex] === target[targetIndex]) {
      targetIndex++;
    }
    sourceIndex++;
  }
  return targetIndex === targetLen;
}

function capitalizeControlValue(form, control_name) {
  form.events.on("Change", function(name, value) {
    if (name == control_name) {
      form.getItem(control_name).setValue(value.capitalize(true));
    }
  }); 
}

String.prototype.capitalize = function (lower) {
  return (lower ? this.toLowerCase() : this).replace(/(?:^|\s|['`‘’.-])[^\x00-\x60^\x7B-\xDF](?!(\s|$))/g, function (a) {
    return a.toUpperCase();
  });
};

$(document).ready(function () {
  if (isEnabledAndActive()) {
    if (debug) console.info("Loading Minty Seed Lib");
    initMintySeedLibData();
  } else {
    err('Minty Seed Library Blocked - Insufficient Permissions' );    
  }
});

$(function() {
  $('.image_tooltip').each(function() {
      var image = $('<img src="' + $(this).data().image + '" style="display:none"></img>');
      $('body').append(image);
      $(image).css({
          position: "absolute",
          top: $(this).position().top + $(this).height(),
          left: $(this).position().left + 10
      });
      for (var prop in $(this).data()) {
          if (prop != "image") {
              $(image).css(prop, $(this).data()[prop]);
          }
      };
      $(this).hover(
          function() { $(image).fadeIn(); },
          function() { $(image).fadeOut(); }
      );
  });
});