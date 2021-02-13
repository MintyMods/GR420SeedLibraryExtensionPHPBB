
$(document).ready(function () {
  if ($('#minty_seed_grid').length > 0) {
    console.info("Loading Minty Seed Lib");
    buildSeedGrid();
  }
});

const expire = 3000;
const labelPosition = "left";
const labelWidth = 120;
const WINDOW_WIDTH = 600;
const GRID_SELECT_URL = "GRID_SELECT_RECORDS";
const GRID_DELETE_URL = "GRID_DELETE_RECORD";
const SEED_POST_URL = "minty_sl_seeds";
const BREEDER_POST_URL = "minty_sl_breeder";
const BREEDER_UPLOAD_URL = "BREEDER_UPLOAD";
const BREEDER_SELECT_URL = "BREEDER_SELECT_RECORD";
const BREEDER_ID = "breeder_id";
const GENETICS = "minty_sl_genetics";
const SMELLS = "minty_sl_smells";
const EFFECTS = "minty_sl_effects";
const TASTES = "minty_sl_tastes";
const METATAGS = "minty_sl_meta_tags";
const AWARDS = "minty_sl_awards";
const COMBO_CONTROLS = [GENETICS, SMELLS, EFFECTS, TASTES, METATAGS, AWARDS];

const flowering_type_options = {
  cols: [
    { type: "radioButton", text: "Auto", value: "A", },
    { type: "radioButton", text: "Photo", value: "P" },
  ]
};

const sex_options = {
  cols: [
    { type: "radioButton", text: "Regular", value: "R", },
    { type: "radioButton", text: "Female", value: "F" }
  ]
};

const indoor_outdoor_options = {
  cols: [
    { id: "indoor_yn", type: "checkbox", text: "Indoor", },
    { id: "outdoor_yn", type: "checkbox", text: "Outdoor", },
  ]
};

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
var focusedControl, oldSeedWindowSize, oldSeedWindowPosition = null;
var fullScreenWindow = false;

function showAddBreederWindow() {
  if (!breederWindow) {
    buildBreederWindow();
  }
  breederWindow.show();
  breederForm.setFocus("breeder_name");
}

function buildBreederWindow() {
  if (!breederForm) {
    buildBreederForm();
  }
  breederWindow = new dhx.Window({ width: WINDOW_WIDTH, height: 380, title: "Add New Breeder", modal: true, resizable: true, movable: true, closable: false, header: true, footer: true, });
  breederWindow.footer.data.add([
    { type: "spacer" },
    { id: "breeder_save", type: "button", value: "Save", view: "flat", color: "primary", icon: "dxi dxi-checkbox-marked-circle", },
    { id: "breeder_cancel", type: "button", value: "Cancel", view: "flat", color: "secondary", icon: "dxi dxi-close-circle", },
    { id: "breeder_delete", type: "button", disabled: true, value: "Delete", view: "flat", color: "danger", icon: "dxi dxi-alert-circle", },
  ], 0);

  breederWindow.footer.events.on("click", function (id) {
    switch (id) {
      case 'breeder_cancel': {
        breederWindow.hide();
        break;
      }
      case 'breeder_save': {
        if (breederForm.validate()) {
          breederForm.send(BREEDER_POST_URL, "POST", true).then(breederFormSaved).catch(function (e) {
            err(e.statusText);
          });
        } else {
          breederForm.setFocus("breeder_name");
          err("Validation Failed");
        }
        break;
      }
    }
  });
  breederWindow.attach(breederForm);
}

function buildBreederForm() {
  breederForm = new dhx.Form(null, {
    rows: [
      { id: BREEDER_ID, type: "input", hidden: true, },
      { id: "breeder_name", type: "input", validation: validateBreeder, required: true, label: "Name", labelPosition, labelWidth, errorMessage: "Breeder name is a required field",  },
      { id: "breeder_desc", type: "textarea", label: "Description", labelPosition, labelWidth, },
      { id: "breeder_url", type: "input", label: "URL", labelPosition, labelWidth, },
      // { id: "breeder_logo", type: "simpleVault", singleRequest:true, target: BREEDER_UPLOAD_URL, fieldName: "breeder_logo", label: "Logo", labelInline: true, labelPosition, labelWidth, },
      {  id: "sponsor_yn", type: "checkbox", label: "Sponsor",labelInline: true, labelPosition, labelWidth, },
    ]
  })
}

function breederFormSaved(response) {
  var json = JSON.parse(response);
  if (json.saved) {
    seedForm.setValue(json.data);
    msg("Breeder Details Saved");
    breederForm.clear();
    breederWindow.hide();
  } else {
    err("Failed to save Breeder Details!");
  }
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

function buildAddSeedButton() {
  var buttons = new dhx.Form("minty_add_button", {
    css: "dhx_widget--bordered",
    rows: [{
      align: "end",
      cols: [{ name: "add_button", type: "button", text: "New", size: "medium", view: "flat", color: "primary", icon: "dxi dxi-plus-circle", }]
    }]
  });
  buttons.getItem("add_button").events.on("Click", function (events) {
    addNewSeedGridRecord();
  });
}

function showSeedWindow() {
  if (!seedWindow) {
    buildSeedWindow();
  }
  seedWindow.show();
}

function buildSeedGrid() {
  seedGrid = new dhx.Grid("minty_seed_grid", {
    columns: [
      { width: 50, id: "id", hidden: true, header: [{ text: "ID" }] },
      { width: 50, id: "breeder_id", hidden: true, header: [{ text: "Breeder ID" }] },
      { width: 150, id: "breeder_name", header: [{ text: "Breeder" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 150, id: "seed_name", type: "string", header: [{ text: "Name" }, { content: "inputFilter" }], type: "string", editorType: "input" },
      { width: 100, id: "flowering_type", header: [{ text: "Type" }, { content: "comboFilter" }], type: "string", editorType: "combobox", options: [' ', 'Regular', 'Feminised', 'Auto'] },
      { width: 80, id: "sex", header: [{ text: "Sex" }, { content: "comboFilter" }], type: "string", editorType: "combobox", options: [' ', 'Male', 'Female'] },
      { width: 50, id: "indoor_yn", type: "boolean", editorType: "checkbox", header: [{ text: "Indoor" }] },
      { width: 50, id: "outdoor_yn", type: "boolean", editorType: "checkbox", header: [{ text: "Outdoor" }] },
      { width: 110, id: "flowering_time", header: [{ text: "Flowering Time" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "harvest_month", type: "date", dateFormat: "%M", header: [{ text: "Harvest Month" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "thc", type: "string", header: [{ text: "THC" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "cbd", type: "string", header: [{ text: "CBD" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "indica", type: "string", header: [{ text: "Indica" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "sativa", type: "string", header: [{ text: "Sativa" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "ruderalis", type: "nstring", header: [{ text: "Ruderalis" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "yeild_indoors", type: "string", header: [{ text: "Indoor Yeild" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "yeild_outdoors", type: "string", header: [{ text: "Outdoor Yeild" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "height_indoors", type: "string", header: [{ text: "Indoor Height" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 110, id: "height_outdoors", type: "string", header: [{ text: "Outdoor Height" }, { content: "comboFilter" }], type: "string", editorType: "input" },
      { width: 200, id: GENETICS, header: [{ text: "Genetics" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 200, id: SMELLS, header: [{ text: "Smells" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 200, id: TASTES, header: [{ text: "Tastes" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 200, id: EFFECTS, header: [{ text: "Effects" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 200, id: AWARDS, header: [{ text: "Awards" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 200, id: METATAGS, header: [{ text: "Effects" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 200, id: "seed_desc", header: [{ text: "Desc" }, { content: "inputFilter" }] },
      { width: 200, id: "forum_url", header: [{ text: "Forum Link" }] },
    ],
    editable: false,
    autoEmptyRow: false,
    height: 520,
    multiselection: false,
    sortable: true,
    selection: "row",
    resizable: true,
  });
  seedGrid.data.load(new dhx.LazyDataProxy(GRID_SELECT_URL, { limit: 15, prepare: 0, delay: 10, from: 0 }));
  buildAddSeedButton();
  buildGridDoubleClickAction();
  buildSeedGridContextMenu();
}

function buildGridDoubleClickAction() {
  seedGrid.events.on("cellDblClick", function (row, column, e) {
    editSeedGridRecord(row);
    e.preventDefault();
  });
}

function editSeedGridRecord(row) {
  seedGrid.selection.setCell(row.id);
  let parsed = {
    seed_id: row.id,
    harvest_month: parseHarvestMonth(row.harvest_month),
  }
  if (!seedWindow) {
    buildSeedWindow();
  }
  seedForm.setValue(Object.assign(row, parsed));
  dhx.awaitRedraw().then(function () {
    showSeedWindow();
  });
}

function buildSeedForm() {
  seedForm = new dhx.Form(null, {
    css: "dhx_widget--bordered",
    rows: [
      { name: "seed_id", type: "text", label: "ID", hidden: true, },
      {
        cols: [
          { name: BREEDER_ID, filter: fuzzySearch, type: "combo", width: 340, label: "Breeder", labelPosition, required: true, labelWidth, errorMessage: "You must select a valid breeder from the list" },
          { name: "add_breeder_button", type: "button", text: "Add New Breeder", size: "medium", width: 160, view: "flat", icon: "dxi dxi-plus", color: "secondary", view: "link", },]
      },
      { name: "seed_name", type: "input", label: "Name", labelPosition, labelWidth, required: true, placeholder: "Name of the Plant?", errorMessage: "Plant name is mandatory to save a new record" },
      { name: "flowering_type", type: "radioGroup", options: flowering_type_options, required: true, label: "Type", labelWidth, labelPosition, errorMessage: "Flowering type is a required field", },
      { name: "sex", type: "radioGroup", options: sex_options, required: true, label: "Sex", errorMessage: "Sex is a required field", labelWidth, labelPosition, },
      { name: "indoor_outdoor", type: "checkboxGroup", options: indoor_outdoor_options, label: "Environment", labelWidth, labelPosition, labelInline: true, },
      { name: "thc", type: "input", label: "THC", labelPosition, labelWidth, },
      { name: "cbd", type: "input", label: "CBD", labelPosition, labelWidth, },
      { name: "indica", type: "input", label: "Indica", labelPosition, labelWidth, },
      { name: "sativa", type: "input", label: "Sativa", labelPosition, labelWidth, },
      { name: "ruderalis", type: "input", label: "Ruderalis", labelPosition, labelWidth, },
      { name: "yeild_indoors", type: "input", label: "Indoor Yeild", labelPosition, labelWidth, },
      { name: "yeild_outdoors", type: "input", label: "Outdoor Yeild", labelPosition, labelWidth, },
      { name: "height_indoors", type: "input", label: "Indoor Height", labelPosition, labelWidth, },
      { name: "height_outdoors", type: "input", label: "Outdoor Height", labelPosition, labelWidth, },
      { name: "flowering_time", type: "input", label: "Flowering Time", labelPosition, labelWidth, },
      { name: "harvest_month", type: "select", options: month_options, label: "Harvest Month", labelPosition, labelWidth, },
      { name: "seed_desc", type: "textarea", label: "Description", labelPosition, labelWidth, },
      { name: GENETICS, filter: fuzzySearch, type: "combo", multiselection: true, label: "Genetics", labelPosition, labelWidth, },
      { name: AWARDS, filter: fuzzySearch, type: "combo", multiselection: true, label: "Awards", labelPosition, labelWidth, },
      { name: SMELLS, filter: fuzzySearch, type: "combo", multiselection: true, label: "Smell", labelPosition, labelWidth, },
      { name: TASTES, filter: fuzzySearch, type: "combo", multiselection: true, label: "Taste", labelPosition, labelWidth, },
      { name: EFFECTS, filter: fuzzySearch, type: "combo", multiselection: true, label: "Effect", labelPosition, labelWidth, },
      { name: METATAGS, filter: fuzzySearch, type: "combo", multiselection: true, label: "Tags", labelPosition, labelWidth, },
    ]
  });
  buildSeedFormEvents();
  processComboControls(COMBO_CONTROLS, seedForm);
  seedForm.getItem(BREEDER_ID).getWidget().data.load(BREEDER_ID);
}

function buildSeedFormEvents() {
  seedForm.getItem("add_breeder_button").events.on("Click", showAddBreederWindow);
  seedForm.events.on("AfterValidate", function(name, value, isValid) {
     if (!isValid && !focusedControl) {
        focusedControl = name;
      }
  }); 
  
  seedForm.events.on("beforeChangeProperties", function(name, properties) {
    console.log(name, properties);
  });

}

function buildSeedGridContextMenu() {
  var seedGridContextMenu = new dhx.ContextMenu(null, { css: "dhx_widget--bg_gray" });
  var contextmenu_data = [
    { "id": "grid_row_add", "icon": "dxi dxi-plus", "value": "New" },
    { "id": "grid_row_edit", "icon": "dxi dxi-pencil", "value": "Edit" },
    { "id": "grid_row_delete", "icon": "dxi dxi-delete", "value": "Delete" }
  ];
  seedGridContextMenu.data.parse(contextmenu_data);
  seedGrid.events.on("CellRightClick", function (row, column, e) {
    seedGrid.selection.setCell(row.id);
    e.preventDefault();
    seedGridContextMenu.showAt(e);
  });

  seedGridContextMenu.events.on("Click", function (option, e) {
    var cell = seedGrid.selection.getCell();
    switch (option) {
      case 'grid_row_add':
        addNewSeedGridRecord();
        break;
      case 'grid_row_edit':
        editSeedGridRecord(cell.row);
        break;
      case 'grid_row_delete':
        deleteSeedGridRecord(cell.row);
        break;
    }
  });
}

function addNewSeedGridRecord() {
  seedForm.clear();
  showSeedWindow();
  dhx.awaitRedraw().then(function () {
    seedForm.setFocus(BREEDER_ID);
  });
}

function deleteSeedGridRecord(row) {
  dhx.confirm({
    header: "Permanently Delete Record - Are you sure?",
    text: "Are you sure you want to delete the database entry for '" + row.seed_name + "' by " + row.breeder_name,
  }).then(function (confirmed) {
    if (confirmed) {
      const url = GRID_DELETE_URL + '?seed_id=' + row.id;
      dhx.ajax.get(url).then(function (result) {
        if (result) {
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
}

function reloadSeedGridRows() {
  seedGrid.data.removeAll();
  seedGrid.data.load(new dhx.LazyDataProxy(GRID_SELECT_URL, { limit: 15, prepare: 0, delay: 25, from: 0 }));
  seedGrid.paint();
  dhx.awaitRedraw().then(function () {
    seedGrid.scrollTo("0", "seed_name");
  });
}

function buildSeedWindow() {
  if (!seedForm) {
    buildSeedForm();
  }
  seedWindow = new dhx.Window({ height: 600, width: WINDOW_WIDTH, title: "Add New Seed Entry", modal: true, resizable: true, movable: true, closable: false, header: true, footer: true, });
  buildSeedWindowToolbar();
  buildSeedWindowFooter();
  seedWindow.attach(seedForm);
}

function buildSeedWindowFooter() {
  seedWindow.footer.data.add([
    { type: "spacer" },
    { id: "save_button", type: "button", icon: "dxi dxi-checkbox-marked-circle", view: "flat", size: "medium", color: "primary", value: "Save", submit: true, },
    { id: "save_new_button", type: "button", value: "Save & New", size: "medium", view: "flat", color: "primary", icon: "dxi dxi-plus-circle", },
    { id: "cancel_button", type: "button", icon: "dxi dxi-close-circle", size: "medium", color: "secondary", value: "Cancel", },
  ], 0);
  seedWindow.footer.events.on("click", function (id) {
    if (id === "cancel_button") {
      seedWindowClose();
    } else if (id === "save_new_button") {
      seedFormSaveNew();
    } else if (id === "save_button") {
      seedFormSave();
    }
  });
}

function seedFormSave() {
  saveSeedFormRecord(function(){
    seedWindowClose();
  });
}

function seedFormSaveNew() {
  saveSeedFormRecord(function(){
    var breeder_id = seedForm.getValue(BREEDER_ID);
    seedForm.clear();
    seedForm.setValue(BREEDER_ID, breeder_id);     
    seedForm.setFocus("seed_name");
  });  
}

function seedWindowClose() {
  seedWindow.hide();
}

function buildSeedWindowToolbar() {
  seedWindow.header.data.add([
    { type: "spacer" },
    { id: "close", icon: "dxi dxi-close" },
    { id: "save", icon: "dxi dxi-check" },
    { id: "save_new", icon: "dxi dxi-plus" },
    { id: "fullscreen", icon: "dxi dxi-arrow-expand" },
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
}

function seedWindowToggleFullScreen() {
  fullScreenWindow = !fullScreenWindow;
  seedWindowDisplayFullScreen(fullScreenWindow);
}

function seedWindowDisplayFullScreen(full) {
  if (full) {
    seedWindow.header.data.update("fullscreen", { icon: "dxi dxi-arrow-collapse" });
    oldSeedWindowSize = seedWindow.getSize();
    oldSeedWindowPosition = seedWindow.getPosition();
    seedWindow.setFullScreen();
  } else {
    seedWindow.header.data.update("fullscreen", { icon: "dxi dxi-arrow-expand" });
    seedWindow.setSize(oldSeedWindowSize.width, oldSeedWindowSize.height);
    seedWindow.setPosition(oldSeedWindowPosition.left,oldSeedWindowPosition.top);
  }
}

function saveSeedFormRecord(callback) {
  if (seedForm.validate()) {
    seedForm.send(SEED_POST_URL, "POST", true).then(function (result) {
        focusedControl = null;  
        if (callback) callback(result);
    }).catch(function(e){
      err(e.statusText, e);
    });
  } else {
    err("Failed to validate form");
    seedForm.setFocus(focusedControl ? focusedControl : BREEDER_ID); 
  }
}

function processComboControls(controls) {
  controls.forEach(function (name) {
    processComboControl(name);
  });
}

function processComboControl(name) {
  let control = seedForm.getItem(name);
  let widget = control.getWidget();
  widget.data.load(name);
  addComboEvents(widget);
}

function addComboEvents(combobox) {
  combobox.events.on("Input", function (value) {
    this._input_value = value;
  });
  combobox.events.on("BeforeClose", function () {
    const id = "U:" + Math.floor(Math.random() * 10000);
    const value = this._input_value;
    if (value && !this.getValue(true).includes(value)) {
      this.data.add({ id, value }, 0);
      dhx.awaitRedraw().then(function () {
        this.setValue(this.getValue(true).push(id));
        this.paint();
        dhx.awaitRedraw().then(function () { this.focus(); }.bind(this));
      }.bind(this));
    }
    this._input_value = undefined;
  });
}

function parseHarvestMonth(value) {
  return (value != '') ? value.substring(0, 3).toLowerCase() : value;
}

function clean(text) {
  return text ? text.trim().toLowerCase() : "";
}

function msg(text, debug) {
  console.log(text,  debug ? debug : '');
  dhx.message({ text, css: "dhx_message--success", icon: "dxi-checkbox-marked-circle", expire });
}

function err(text, debug) {
  console.log(text, debug ? debug : '');
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