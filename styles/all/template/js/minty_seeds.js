
const GRID_SELECT = "./GRID_SELECT";

var dataset = "";
var breeders_options = [
  {
    value: "",
    content: "Select Breeder"
  },
  {
    value: "GC",
    content: "Growers Choice"
  },
  {
    value: "SS",
    content: "Seed Stockers"
  },
  {
    value: "DF",
    content: "Dynofem"
  },
  {
    value: "RQS",
    content: "Royal Queen Seeds"
  }
];
var flowering_type_options = {
  cols: [
    {
      type: "radioButton",
      text: "Auto",
      value: "A",
    },
    {
      type: "radioButton",
      text: "Photo",
      value: "P"
    },
    {
      type: "radioButton",
      text: "Regular",
      value: "R"
    },
  ]
};
var sex_options = {
  cols: [
    {
      type: "radioButton",
      text: "Male",
      value: "M",
    },
    {
      type: "radioButton",
      text: "Female",
      value: "F"
    }
  ]
};

var genetics_options =  [
  { id: "1", value: "@todo" },
  { id: "2", value: "Amnesia Haze" },
  { id: "3", value: "Gorilla Glue 4" },
  { id: "4", value: "Sweet ZZ" },
  { id: "5", value: "Diesel Auto" },
];
var smells_options =  [
  { id: "1", value: "@todo" },
  { id: "2", value: "Citrus" },
  { id: "3", value: "Earthy" },
  { id: "4", value: "Pine" },
  { id: "5", value: "Pungent" },
];
var tastes_options =  [
  { id: "1", value: "@todo" },
  { id: "2", value: "Sweet" },
  { id: "3", value: "Citrus" },
  { id: "4", value: "Floral" },
  { id: "5", value: "Like Shit" },
];
var effects_options =  [
  { id: "1", value: "@todo" },
  { id: "2", value: "Calming" },
  { id: "3", value: "Clear" },
  { id: "4", value: "Laughter" },
  { id: "5", value: "Uplifting" },
];
var meta_tag_options =  [
  { id: "1", value: "@todo" },
  { id: "2", value: "Short" },
  { id: "3", value: "Medium" },
  { id: "4", value: "Tall" },
  { id: "5", value: "Mold Resistant" },
];
var indoor_outdoor_options = {
  cols: [
    {
      id: "indoor_yn",
      type: "checkbox",
      text: "Indoor",
    },
    {
      id: "outdoor_yn",
      type: "checkbox",
      text: "Outdoor",
    },
  ]
};
var month_options = [
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
]

var grid = new dhx.Grid("grid", {
    columns: [
      { width: 50,  id: "id", header: [{ text: "ID" }]  },
      { width: 150, id: "seed_name", type:"string", header: [{ text: "Name" }, { content: "inputFilter" }], type: "string", editorType: "input" },
      { width: 150, id: "breeder_name", header: [{ text: "Breeder" }, { content: "comboFilter" }], type: "string", editorType: "combobox", options: breeders_options },
      { width: 100, id: "flowering_type", header: [{ text: "Type" }, { content: "comboFilter" }], type: "string", editorType: "combobox", options: [' ','Regular','Feminised','Auto'] },
      { width: 80, id: "sex", header: [{ text: "Sex" }, { content: "comboFilter" }], type: "string", editorType: "combobox", options: [' ','Male','Female'] },
      { width: 50, id: "indoor_yn", type:"boolean", editorType: "checkbox", header: [{ text: "Indoor" } ] }, 
      { width: 50, id: "outdoor_yn", type:"boolean", editorType: "checkbox", header: [{ text: "Outdoor" }] },
      { width: 110, id: "flowering_time_days", header: [{ text: "Flowering Time" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 110, id: "harvest_outdoors", type: "date", dateFormat: "%M", header: [{ text: "Harvest Month" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 110, id: "thc_percentage", type:"number", header: [{ text: "THC %" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 110, id: "cbd_percentage", type:"number", header: [{ text: "CBD %" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 110, id: "indica_percentage", type:"number", header: [{ text: "Indica %" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 110, id: "sativa_percentage", type:"number", header: [{ text: "Sativa %" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 110, id: "ruderalis_percentage", type:"number", header: [{ text: "Ruderalis %" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 110, id: "yeild_indoors_grams", type:"number", header: [{ text: "Indoor Yeild" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 110, id: "yeild_outdoors_grams", type:"number", header: [{ text: "Outdoor Yeild" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 110, id: "height_indoors_mm", type:"number", header: [{ text: "Indoor Height" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 110, id: "height_outdoors_mm", type:"number", header: [{ text: "Outdoor Height" }, { content: "comboFilter" }], type: "string", editorType: "combobox" },
      { width: 200, id: "minty_sl_genetics", header: [{ text: "Genetics" }, { content: "comboFilter" }], type: "string", editorType: "combobox"  },
      { width: 200, id: "minty_sl_smells", header: [{ text: "Smells" }, { content: "comboFilter" }], type: "string", editorType: "combobox"  },
      { width: 200, id: "minty_sl_tastes", header: [{ text: "Tastes" }, { content: "comboFilter" }], type: "string", editorType: "combobox"  },
      { width: 200, id: "minty_sl_effects", header: [{ text: "Effects" }, { content: "comboFilter" }], type: "string", editorType: "combobox"  },
      { width: 200, id: "minty_sl_meta_tag", header: [{ text: "Effects" }, { content: "comboFilter" }], type: "string", editorType: "combobox"  },
      { width: 200, id: "seed_desc", header: [{ text: "Desc" }, { content: "inputFilter" }] },
      { width: 200, id: "forum_url", header: [{ text: "Forum Link" }] },
    ],
    editable: true,
    autoEmptyRow:false,
    height: 620,  
    // leftSplit:2, 
    multiselection:true,
    selection:"row",    
    resizable: true, 
  });

  var lazyDataProxy = new dhx.LazyDataProxy(GRID_SELECT, {
    limit: 15,
    prepare: 0,
    delay: 10,
    from: 0
  });

  grid.data.events.on("AfterLazyLoad", function (from, count) {
    console.log("AfterLazyLoad");
  });
  grid.data.events.on("AfterAdd", function(newItem){
    console.log("A new item is added");
  });
  grid.data.events.on("LoadError", function(error){
    console.log("LoadError");
  });

  grid.data.load(lazyDataProxy);
  
  
  var buttons = new dhx.Form("button_container", {
    css: "dhx_widget--bordered",
    rows: [
        {
          align: "end",
          cols: [
            {
              name: "add_button",
              type: "button",
              text: "New",
              size: "medium",
              view: "flat",
              color: "primary",
              icon:"dxi dxi-plus-circle",
            }
          ]
        }        
    ]
});

const dhxWindow = new dhx.Window({
  title:"Add New Seed Entry",
  modal: false,
  resizable: true,
  header:false,
  movable: true,
  closable:true,
  footer:true,
});
dhxWindow.footer.data.add({
  id: "fullscreen_button",
  type: "button",
  icon:"dxi dxi-arrow-expand",
  view: "link",
  size: "medium",
  color: "secondary",
  value: "Toggle Full Screen",
});
dhxWindow.footer.data.add({
  type: "spacer",
});
dhxWindow.footer.data.add({
  id: "cancel_button",
  type: "button",
  icon:"dxi dxi-close-circle",
  size: "medium",
  color: "secondary",
  value: "Cancel",
});
dhxWindow.footer.data.add({
  id: "save_new_button",
  type: "button",
  value: "Save & New",
  size: "medium",
  view: "flat",
  color: "primary",
  icon:"dxi dxi-plus-circle",
});
dhxWindow.footer.data.add({
  id: "save_button",
  type: "button",
  icon:"dxi dxi-checkbox-marked-circle",
  view: "flat",
  size: "medium",
  color: "primary",
  value: "Save",
  submit: true,
});
dhxWindow.footer.events.on("click", function (id) {
  if (id === "cancel_button") {
      dhxWindow.hide()
  } else if (id === "save_new_button") {
   //@todo
  } else if (id === "save_button") {
    if (form_container.validate()) {
      dhxWindow.hide();
    }
  }
});
buttons.getItem("add_button").events.on("Click", function(events) {
    dhxWindow.show();
});

let isFullScreen = false;
let oldSize = null;
let oldPos = null;

dhxWindow.footer.events.on("click", function (id) {
    if (id === "fullscreen_button") {
        if (isFullScreen) {
            dhxWindow.setSize(oldSize.width, oldSize.height);
            dhxWindow.setPosition(oldPos.left, oldPos.top);
        } else {
            oldSize = dhxWindow.getSize();
            oldPos = dhxWindow.getPosition();
            dhxWindow.setFullScreen();
        }
        isFullScreen = !isFullScreen;
    }
});

  var label_width = 100;
  const form_container = new dhx.Form('form_container', {
    css: "dhx_widget--bordered",
    rows: [
      {
          name: "seed_id",
          type: "text",
          label: "ID",
          value:"@todo",
          hidden:true,
        },
        {
          name: "seed_name",
          type: "input",
          label: "Name",
          labelPosition: "left",
          labelWidth: label_width,
          required: true,          
          placeholder: "Name of the Plant?",
          errorMessage: "Plant name is mandatory to save a new record"
        },
        {
        cols:[
            {
                name: "breeder_id",
                type: "select",
                label: "Breeder *",
                labelPosition: "left",
                required: true,          
                labelWidth: label_width,
                errorMessage: "You must select a valid breeder from the list",
                validation: function(value) {
                  return value !== "";
                },
                options: breeders_options
            },
            {
              name: "add_breader_button",
              type: "button",
              text: "Add New Breader",
              size: "medium",
              width:160,
              view: "flat",
              icon: "dxi dxi-plus",
              color: "secondary",
              view: "link",
               
            },
          ]
        },

        {
          name: "flowering_type",
          type: "radioGroup",
          required: true,
          label: "Type",
          labelWidth: label_width,
          labelPosition: "left",
          options: flowering_type_options,
        }, 
        {
          name: "sex",
          type: "radioGroup",
          required: true,
          label: "Sex",
          labelWidth: label_width,
          labelPosition: "left",
          options: sex_options,
        }, 
         {
          name: "indoor_outdoor",
          type: "checkboxGroup",
          label: "Environment",
          labelWidth: label_width,
          labelPosition: "left",
          options: indoor_outdoor_options,
        },  
          
        {
          name: "thc_percentage",
          type: "slider",
          label: "THC %",
          labelPosition: "left",
          labelWidth: label_width,
          min: 0,
          max: 35,
          step:1,
          tick:5,
          majorTick:10,
          tickTemplate: function(v){
            return v;
          },
          width:'96%',
        },
        {
          name: "cbd_percentage",
          type: "slider",
          label: "CBD %",
          labelPosition: "left",
          labelWidth: label_width,
          min: 0,
          max: 20,
          step:1,
          tick:5,
          majorTick:10,
          tickTemplate: function(v){
            return v;
          },
          width:'96%', 
        },
     
        {
          name: "indica_percentage",
          type: "slider",
          label: "Indica %",
          labelPosition: "left",
          labelWidth: label_width,
          min: 0,
          max: 100,
          step:1,
          tick:5,
          majorTick:10,
          tickTemplate: function(v){
            return v;
          },
          width:'96%', 
        },
        {
          name: "sativa_percentage",
          type: "slider",
          label: "Sativa %",
          labelPosition: "left",
          labelWidth: label_width,
          min: 0,
          max: 100,
          step:1,
          tick:5,
          majorTick:10,
          tickTemplate: function(v){
            return v;
          },
          width:'96%', 
        },
         
        {
          name: "ruderalis_percentage",
          type: "slider",
          label: "Ruderalis %",
          labelPosition: "left",
          labelWidth: label_width,
          min: 0,
          max: 100,
          step:1,
          tick:5,
          majorTick:10,
          tickTemplate: function(v){
            return v + "";
          },
          width:'96%', 
        },
        {
          cols:[
            {
              name: "yeild_indoors_grams",
              type: "input",
              label: "Indoor Yeild",
              labelPosition: "left",
              labelWidth: label_width,
              placeholder: "gram per m2",
            }, 
            {
              name: "yeild_outdoors_grams",
              type: "input",
              label: "Outdoor Yeild",
              labelPosition: "left",
              labelWidth: label_width,
              placeholder: "gram per plant",
            }, 
          ]
        },
        {
          cols:[
            {
              name: "height_indoors_mm",
              type: "input",
              label: "Indoor Height",
              labelPosition: "left",
              labelWidth: label_width,
              placeholder: "mm",
            }, 
            {
              name: "height_outdoors_mm",
              type: "input",
              label: "Outdoor Height",
              labelPosition: "left",
              labelWidth: label_width,
              placeholder: "mm",
            }, 
          ]
        },
        {
          cols:[
            {
              name: "flowering_time_days",
              type: "input",
              label: "Flowering Time",
              labelPosition: "left",
              labelWidth: label_width+30,
              placeholder: "Days",
            },        
            {
              name: "harvest_month_outdoors",
              type: "select",
              label: "Harvest Outdoors",
              width:250,
              labelPosition: "left",
              labelWidth: label_width+30,
              options: month_options
            },
          ]
        },    
        {
          name: "minty_sl_meta_tags",
          type: "combo",
          label: "Tags",
          labelPosition: "left",
          labelWidth: label_width,
          multiselection: true,
          data: meta_tag_options,
        },        
        {
          name: "seed_desc",
          type: "textarea",
          label: "Description",
          labelPosition: "left",
          labelWidth: label_width,
        },               
        {
          name: "minty_sl_genetics",
          type: "combo",
          label: "Genetics",
          labelPosition: "left",
          labelWidth: label_width,
          multiselection: true,
          data: genetics_options,
        },
        {
          name: "minty_sl_smells",
          type: "combo",
          label: "Smell",
          labelPosition: "left",
          labelWidth: label_width,
          multiselection: true,
          data: smells_options,
        },
        {
          name: "minty_sl_tastes",
          type: "combo",
          label: "Taste",
          labelPosition: "left",
          labelWidth: label_width,
          multiselection: true,
          data: tastes_options,
        },
        {
          name: "minty_sl_effects",
          type: "combo",
          label: "Effect",
          labelPosition: "left",
          labelWidth: label_width,
          multiselection: true,
          data: effects_options,
        },

        {
          name: "forum_url",
          type: "input",
          label: "Forum URL",
          labelPosition: "left",
          labelWidth: label_width,   
          placeholder: "Forum URL"
        },        
 
    ]
});

dhxWindow.attach(form_container);
