(function() {
  rivets.binders.input = {
    publishes: true,
    routine: rivets.binders.value.routine,
    bind: function(el) {
      return $(el).bind('input.rivets', this.publish);
    },
    unbind: function(el) {
      return $(el).unbind('input.rivets');
    }
  };

  rivets.configure({
    prefix: "rv",
    adapter: {
      subscribe: function(obj, keypath, callback) {
        callback.wrapped = function(m, v) {
          return callback(v);
        };
        return obj.on('change:' + keypath, callback.wrapped);
      },
      unsubscribe: function(obj, keypath, callback) {
        return obj.off('change:' + keypath, callback.wrapped);
      },
      read: function(obj, keypath) {
        if (keypath === "cid") {
          return obj.cid;
        }
        return obj.get(keypath);
      },
      publish: function(obj, keypath, value) {
		
		  
        if (obj.cid) {
          return obj.set(keypath, value);
        } else {
          return obj[keypath] = value;
        }
		return obj.on('change:' + keypath, callback.wrapped);
      }
    }
  });

}).call(this);

(function() {
  var BuilderView, EditFieldView, Formbuilder, FormbuilderCollection, FormbuilderModel, ViewFieldView, _ref, _ref1, _ref2, _ref3, _ref4,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  FormbuilderModel = (function(_super) {
    __extends(FormbuilderModel, _super);

    function FormbuilderModel() {
      _ref = FormbuilderModel.__super__.constructor.apply(this, arguments);
      return _ref;
    }

    FormbuilderModel.prototype.sync = function() { };

    FormbuilderModel.prototype.indexInDOM = function() {
      var $wrapper,
        _this = this;
      $wrapper = $(".fb-field-wrapper").filter((function(_, el) {
        return $(el).data('cid') === _this.cid;
      }));
      return $(".fb-field-wrapper").index($wrapper);
    };

    FormbuilderModel.prototype.is_input = function() {
      return Formbuilder.inputFields[this.get(Formbuilder.options.mappings.FIELD_TYPE)] != null;
    };

    return FormbuilderModel;

  })(Backbone.DeepModel);

  FormbuilderCollection = (function(_super) {
    __extends(FormbuilderCollection, _super);

    function FormbuilderCollection() {
      _ref1 = FormbuilderCollection.__super__.constructor.apply(this, arguments);
      return _ref1;
    }

    FormbuilderCollection.prototype.initialize = function() {
      return this.on('add', this.copyCidToModel);
    };

    FormbuilderCollection.prototype.model = FormbuilderModel;

    FormbuilderCollection.prototype.comparator = function(model) {
      return model.indexInDOM();
    };

    FormbuilderCollection.prototype.copyCidToModel = function(model) {
	    
		var myCid=model.cid; 
		myCid=myCid.replace('c','');
		myCid=parseInt(myCid);
		
		var allCids=new Array();
	    
		var isduplicate = this.find(function(listModel) {
             if(listModel.get('cid')) {
				  var listCid=listModel.get('cid');
				  listCid=listCid.replace('c','');
				  listCid=parseInt(listCid);
				  allCids.push(listCid);
			   }
        });
		
		if(_.indexOf(allCids, myCid)>0) { 
		  var maxCid=_.max(allCids);
		  maxCid++;
		  model.cid='c'+maxCid;
		}
	
       return model.attributes.cid = model.cid;
    };

    return FormbuilderCollection;

  })(Backbone.Collection);

  ViewFieldView = (function(_super) {
    __extends(ViewFieldView, _super);

    function ViewFieldView() {
      _ref2 = ViewFieldView.__super__.constructor.apply(this, arguments);
      return _ref2;
    }

    ViewFieldView.prototype.className = "fb-field-wrapper";

    ViewFieldView.prototype.events = {
      'click .subtemplate-wrapper': 'focusEditView',
      'click .js-duplicate': 'duplicate',
      'click .js-clear': 'clear'
    };

    ViewFieldView.prototype.initialize = function(options) {
      this.parentView = options.parentView;
      this.listenTo(this.model, "change", this.render);
      return this.listenTo(this.model, "destroy", this.remove);
    };

    ViewFieldView.prototype.render = function() {
      this.$el.addClass('response-field-' + this.model.get(Formbuilder.options.mappings.FIELD_TYPE)).data('cid', this.model.cid).html(Formbuilder.templates["view/base" + (!this.model.is_input() ? '_non_input' : '')]({
        rf: this.model
      }));
      return this;
    };

    ViewFieldView.prototype.focusEditView = function() {
      return this.parentView.createAndShowEditView(this.model);
    };

    ViewFieldView.prototype.clear = function(e) {
      var cb, x,
        _this = this;
      e.preventDefault();
      e.stopPropagation();
      cb = function() {
        _this.parentView.handleFormUpdate();
        return _this.model.destroy();
      };
      x = Formbuilder.options.CLEAR_FIELD_CONFIRM;
      switch (typeof x) {
        case 'string':
          if (confirm(x)) {
            return cb();
          }
          break;
        case 'function':
          return x(cb);
        default:
          return cb();
      }
    };

    ViewFieldView.prototype.duplicate = function() {
      var attrs;
      attrs = _.clone(this.model.attributes);
      delete attrs['id'];
      attrs['label'] += ' Copy';
      return this.parentView.createField(attrs, {
        position: this.model.indexInDOM() + 1
      });
    };

    return ViewFieldView;

  })(Backbone.View);

  EditFieldView = (function(_super) {
    __extends(EditFieldView, _super);

    function EditFieldView() {
      _ref3 = EditFieldView.__super__.constructor.apply(this, arguments);
      return _ref3;
    }

    EditFieldView.prototype.className = "edit-response-field";

    EditFieldView.prototype.events = {
      'click .js-add-option': 'addOption',
      'click .js-remove-option': 'removeOption', 
	  'change .js-default-updated': 'defaultUpdated',
	  'click .js-default-updated': 'defaultUpdatedByClick',
      'input .option-label-input': 'forceRender',
	  'input .option-value-input': 'forceRender'
    };

    EditFieldView.prototype.initialize = function(options) {
      this.parentView = options.parentView;
      return this.listenTo(this.model, "destroy", this.remove);
    };

    EditFieldView.prototype.render = function() {
      this.$el.html(Formbuilder.templates["edit/base" + (!this.model.is_input() ? '_non_input' : '')]({
        rf: this.model
      }));
      rivets.bind(this.$el, {
        model: this.model
      });
      return this;
    };

    EditFieldView.prototype.remove = function() {
      this.parentView.editView = void 0;
      this.parentView.$el.find("[data-target=\"#addField\"]").click();
      return EditFieldView.__super__.remove.apply(this, arguments);
    };

    EditFieldView.prototype.addOption = function(e) {
      var $el, i, newOption, options;
      $el = $(e.currentTarget);
      i = this.$el.find('.option').index($el.closest('.option'));
      options = this.model.get(Formbuilder.options.mappings.OPTIONS) || [];
      newOption = {
        label: "",
		 value: "",
        checked: false
      };
      if (i > -1) {
        options.splice(i + 1, 0, newOption);
      } else {
        options.push(newOption);
      }
      this.model.set(Formbuilder.options.mappings.OPTIONS, options);
      this.model.trigger("change:" + Formbuilder.options.mappings.OPTIONS);
      return this.forceRender();
    };

    EditFieldView.prototype.removeOption = function(e) {
      var $el, index, options;
      $el = $(e.currentTarget);
      index = this.$el.find(".js-remove-option").index($el);
      options = this.model.get(Formbuilder.options.mappings.OPTIONS);
      options.splice(index, 1);
      this.model.set(Formbuilder.options.mappings.OPTIONS, options);
      this.model.trigger("change:" + Formbuilder.options.mappings.OPTIONS);
      return this.forceRender();
    };

    EditFieldView.prototype.defaultUpdated = function(e) {
		
      var $el;
      $el = $(e.currentTarget);
      if (this.model.get(Formbuilder.options.mappings.FIELD_TYPE) !== 'checkboxes') {
        this.$el.find(".js-default-updated").not($el).attr('checked', false).trigger('change');
      }
       return this.forceRender();
    };
	
	 EditFieldView.prototype.defaultUpdatedByClick = function(e) {
		
      var $el;
      $el = $(e.currentTarget);
	   myindex = this.$el.find('.option').index($el.closest('.option'));
	   
      if (this.model.get(Formbuilder.options.mappings.FIELD_TYPE)== 'checkboxes') {
        options = this.model.get(Formbuilder.options.mappings.OPTIONS);
		 i=0;
		 options=_.map(options,function(option){
			if(i==myindex){
			   option.checked=($el.prop("checked"))?true:false;
			}
			i++; 
			return option; 
		   });
		 return this.forceRender();
      }
	  
    };

    EditFieldView.prototype.forceRender = function() {
      return this.model.trigger('change');
    };

    return EditFieldView;

  })(Backbone.View);

  BuilderView = (function(_super) {
    __extends(BuilderView, _super);

    function BuilderView() {
      _ref4 = BuilderView.__super__.constructor.apply(this, arguments);
      return _ref4;
    }

    BuilderView.prototype.SUBVIEWS = [];

    BuilderView.prototype.events = {
      'click .js-save-form': 'saveForm',
      'click .fb-tabs a': 'showTab',
      'click .fb-add-field-types a': 'addField',
      'mouseover .fb-add-field-types': 'lockLeftWrapper',
      'mouseout .fb-add-field-types': 'unlockLeftWrapper'
    };

    BuilderView.prototype.initialize = function(options) {
      var selector;
      selector = options.selector, this.formBuilder = options.formBuilder, this.bootstrapData = options.bootstrapData;
	  if(options.end_point){
	    Formbuilder.options.HTTP_ENDPOINT=options.end_point;
	  }
	  
	  if(options.onAjaxSubmit){
	    Formbuilder.options.onAjaxSubmit=options.onAjaxSubmit;
	  }
	  if(options.onAjaxComplete){
	    Formbuilder.options.onAjaxComplete=options.onAjaxComplete;
	  }
	  
      if (selector != null) {
        this.setElement($(selector));
      }
      this.collection = new FormbuilderCollection;
      this.collection.bind('add', this.addOne, this);
      this.collection.bind('reset', this.reset, this);
      this.collection.bind('change', this.handleFormUpdate, this);
      this.collection.bind('destroy add reset', this.hideShowNoResponseFields, this);
      this.collection.bind('destroy', this.ensureEditViewScrolled, this);
      this.render();
      this.collection.reset(this.bootstrapData);
      return this.bindSaveEvent();
    };

    BuilderView.prototype.bindSaveEvent = function() {
      var _this = this;
      this.formSaved = true;
      //this.saveFormButton = this.$el.find(".js-save-form");
      //this.saveFormButton.attr('disabled', true).text(Formbuilder.options.dict.ALL_CHANGES_SAVED);
      if (!!Formbuilder.options.AUTOSAVE) {
        setInterval(function() {
          return _this.saveForm.call(_this);
        }, 5000);
      }
      return $(window).bind('beforeunload', function() {
        if (_this.formSaved) {
          return void 0;
        } else {
          return Formbuilder.options.dict.UNSAVED_CHANGES;
        }
      });
    };

    BuilderView.prototype.reset = function() {
      this.$responseFields.html('');
      return this.addAll();
    };

    BuilderView.prototype.render = function() {
      var subview, _i, _len, _ref5;
      this.$el.html(Formbuilder.templates['page']());
      this.$fbLeft = this.$el.find('.fb-left');
      this.$responseFields = this.$el.find('.fb-response-fields');
      this.bindWindowScrollEvent();
      this.hideShowNoResponseFields();
	 
      _ref5 = this.SUBVIEWS;
      for (_i = 0, _len = _ref5.length; _i < _len; _i++) {
        subview = _ref5[_i];
        new subview({
          parentView: this
        }).render();
      }
      return this;
    };

    BuilderView.prototype.bindWindowScrollEvent = function() {
      var _this = this;
      return $(window).on('scroll', function() {
        var maxMargin, newMargin;
        if (_this.$fbLeft.data('locked') === true) {
          return;
        }
        newMargin = Math.max(0, $(window).scrollTop() - _this.$el.offset().top);
        maxMargin = _this.$responseFields.height();
        return _this.$fbLeft.css({
          'margin-top': Math.min(maxMargin, newMargin)
        });
      });
    };

    BuilderView.prototype.showTab = function(e) {
      var $el, first_model, target;
      $el = $(e.currentTarget);
      target = $el.data('target');
      $el.closest('li').addClass('active').siblings('li').removeClass('active');
      $(target).addClass('active').siblings('.fb-tab-pane').removeClass('active');
      if (target !== '#editField') {
        this.unlockLeftWrapper();
      }
      if (target === '#editField' && !this.editView && (first_model = this.collection.models[0])) {
        return this.createAndShowEditView(first_model);
      }
    };

    BuilderView.prototype.addOne = function(responseField, _, options) {
      var $replacePosition, view;
      view = new ViewFieldView({
        model: responseField,
        parentView: this
      });
	 
      if (options.$replaceEl != null) {
        return options.$replaceEl.replaceWith(view.render().el);
      } else if ((options.position == null) || options.position === -1) {
        return this.$responseFields.append(view.render().el);
      } else if (options.position === 0) {
        return this.$responseFields.prepend(view.render().el);
      } else if (($replacePosition = this.$responseFields.find(".fb-field-wrapper").eq(options.position))[0]) {
        return $replacePosition.before(view.render().el);
      } else {
        return this.$responseFields.append(view.render().el);
      }
    };

    BuilderView.prototype.setSortable = function() {
      var _this = this;
      if (this.$responseFields.hasClass('ui-sortable')) {
        this.$responseFields.sortable('destroy');
      }
      this.$responseFields.sortable({
        forcePlaceholderSize: true,
        placeholder: 'sortable-placeholder',
        stop: function(e, ui) {
          var rf;
          if (ui.item.data('field-type')) {
            rf = _this.collection.create(Formbuilder.helpers.defaultFieldAttrs(ui.item.data('field-type')),           {
              $replaceEl: ui.item
            });
            _this.createAndShowEditView(rf);
          }
          _this.handleFormUpdate();
          return true;
        },
        update: function(e, ui) {
          if (!ui.item.data('field-type')) {
            return _this.ensureEditViewScrolled();
          }
        }
      });
      return this.setDraggable();
    };

    BuilderView.prototype.setDraggable = function() {
      var $addFieldButtons,
        _this = this;
      $addFieldButtons = this.$el.find("[data-field-type]");
      return $addFieldButtons.draggable({
        connectToSortable: this.$responseFields,
        helper: function() {
          var $helper;
          $helper = $("<div class='response-field-draggable-helper' />");
          $helper.css({
            width: _this.$responseFields.width(),
            height: '80px'
          });
          return $helper;
        }
      });
    };

    BuilderView.prototype.addAll = function() {
      this.collection.each(this.addOne, this);
      return this.setSortable();
    };

    BuilderView.prototype.hideShowNoResponseFields = function() {
      return this.$el.find(".fb-no-response-fields")[this.collection.length > 0 ? 'hide' : 'show']();
    };

    BuilderView.prototype.addField = function(e) {
      var field_type;
      field_type = $(e.currentTarget).data('field-type');
      return this.createField(Formbuilder.helpers.defaultFieldAttrs(field_type));
    };

    BuilderView.prototype.createField = function(attrs, options) {
      var rf;
      rf = this.collection.create(attrs, options);
      this.createAndShowEditView(rf);
      return this.handleFormUpdate();
    };

    BuilderView.prototype.createAndShowEditView = function(model) {
      var $newEditEl, $responseFieldEl;
      $responseFieldEl = this.$el.find(".fb-field-wrapper").filter(function() {
        return $(this).data('cid') === model.cid;
      });
      $responseFieldEl.addClass('editing').siblings('.fb-field-wrapper').removeClass('editing');
      if (this.editView) {
        if (this.editView.model.cid === model.cid) {
          this.$el.find(".fb-tabs a[data-target=\"#editField\"]").click();
          this.scrollLeftWrapper($responseFieldEl);
          return;
        }
        this.editView.remove();
      }
      this.editView = new EditFieldView({
        model: model,
        parentView: this
      });
	  
      $newEditEl = this.editView.render().$el;
      this.$el.find(".fb-edit-field-wrapper").html($newEditEl);
      this.$el.find(".fb-tabs a[data-target=\"#editField\"]").click();
      this.scrollLeftWrapper($responseFieldEl);
      return this;
    };

    BuilderView.prototype.ensureEditViewScrolled = function() {
      if (!this.editView) {
        return;
      }
      return this.scrollLeftWrapper($(".fb-field-wrapper.editing"));
    };

    BuilderView.prototype.scrollLeftWrapper = function($responseFieldEl) {
      var _this = this;
      this.unlockLeftWrapper();
      if (!$responseFieldEl[0]) {
        return;
      }
      return $.scrollWindowTo((this.$el.offset().top + $responseFieldEl.offset().top) - this.$responseFields.offset().top, 200, function() {
        return _this.lockLeftWrapper();
      });
    };

    BuilderView.prototype.lockLeftWrapper = function() {
      return this.$fbLeft.data('locked', true);
    };

    BuilderView.prototype.unlockLeftWrapper = function() {
      return this.$fbLeft.data('locked', false);
    };

    BuilderView.prototype.handleFormUpdate = function() {
      if (this.updatingBatch) {
        return;
      }
	
      this.formSaved = false;
      //return this.saveFormButton.removeAttr('disabled').text(Formbuilder.options.dict.SAVE_FORM);
    };

    BuilderView.prototype.saveForm = function(e) {
      var payload;
      if (this.formSaved) {
        return;
      }
      this.formSaved = true;
      
	  var form_data=Formbuilder.options.onAjaxSubmit.call(this);
	  
      this.collection.sort();
	  //console.log(this.collection.toJSON());
      payload = JSON.stringify({
        fields: this.collection.toJSON(),
		 form:form_data
      });
	  
	  
	  this.formBuilder.trigger('save', payload); // save form first and try to save in server too - samir
      if (Formbuilder.options.HTTP_ENDPOINT) {
        this.doAjaxSave(payload);
      }
      return true;
    };

    BuilderView.prototype.doAjaxSave = function(payload) {
      var _this = this;
	  
      return $.ajax({
        url: Formbuilder.options.HTTP_ENDPOINT,
        type: Formbuilder.options.HTTP_METHOD,
        data: {data:payload},
		 dataType:'json',
        success: function(data) {
 
          //_this.updatingBatch = true;
          Formbuilder.options.onAjaxComplete.call(this,data);
        }
      });
    };

    return BuilderView;

  })(Backbone.View);

  Formbuilder = (function() {
    Formbuilder.helpers = {
      defaultFieldAttrs: function(field_type) {
        var attrs, _base;
        attrs = {};
        attrs[Formbuilder.options.mappings.LABEL] = 'Untitled';
        attrs[Formbuilder.options.mappings.FIELD_TYPE] = field_type;
        attrs[Formbuilder.options.mappings.REQUIRED] = true;
		attrs[Formbuilder.options.mappings.HIDE_LABEL] = false;
		attrs[Formbuilder.options.mappings.GUIDELINE_PLACEHOLDER] = false;
        attrs['field_options'] = {};
        return (typeof (_base = Formbuilder.fields[field_type]).defaultAttributes === "function" ? _base.defaultAttributes(attrs) : void 0) || attrs;
      },
      simple_format: function(x) {
        return x != null ? x.replace(/\n/g, '<br />') : void 0;
      },
      
      placeholderStatus: function(type) {
      	if(type == 'email' || type == 'text' || type == 'paragraph') return true;
      	return false;
      },

      buttonStatus: function(type) {
        if(type == 'submit') return true;
        return false;
      },
      
      initValueStatus: function(type) {
      	if(type == 'file') return false;
      	return true;
      }
      
    };

    Formbuilder.options = {
      BUTTON_CLASS: 'fb-button',
      HTTP_ENDPOINT: '',
      HTTP_METHOD: 'POST',
	  onAjaxSubmit:function(){},
	  onAjaxComplete:function(){},
      AUTOSAVE: true,
      CLEAR_FIELD_CONFIRM: false,
      mappings: {
        SIZE: 'field_options.size',
        UNITS: 'field_options.units',
        LABEL: 'label',
        FIELD_TYPE: 'field_type',
        REQUIRED: 'required',
        HIDE_LABEL: 'hide_label', 
        OPTIONS: 'field_options.options',
        DESCRIPTION: 'field_options.description',
        ERROR: 'field_options.error',
		    HTML: 'field_options.html',
		    HIDDEN: 'field_options.hidden',
		    RGEX: 'field_options.regex',
        INCLUDE_OTHER: 'field_options.include_other_option',
        INCLUDE_BLANK: 'field_options.include_blank_option',
        GUIDELINE_PLACEHOLDER: 'field_options.placeholder',
        RESET_BUTTON: 'field_options.reset_button',
        MIN: 'field_options.min',
        MAX: 'field_options.max',
        MINLENGTH: 'field_options.minlength',
        MAXLENGTH: 'field_options.maxlength',
        LENGTH_UNITS: 'field_options.min_max_length_units'
      },
      dict: {
        ALL_CHANGES_SAVED: 'All changes saved',
        SAVE_FORM: dict['TEXT_SAVE'],
        UNSAVED_CHANGES: dict['TEXT_SAVE_WARN']
      }
    };

    Formbuilder.fields = {};

    Formbuilder.inputFields = {};

    Formbuilder.nonInputFields = {};

    Formbuilder.registerField = function(name, opts) {
      var x, _i, _len, _ref5;
      _ref5 = ['view', 'edit'];
      for (_i = 0, _len = _ref5.length; _i < _len; _i++) {
        x = _ref5[_i];
        opts[x] = _.template(opts[x]);
      }
      opts.field_type = name;
      Formbuilder.fields[name] = opts;
      if (opts.type === 'non_input') {
        return Formbuilder.nonInputFields[name] = opts;
      } else {
        return Formbuilder.inputFields[name] = opts;
      }
    };

    function Formbuilder(opts) {
      var args;
      if (opts == null) {
        opts = {};
      }
      _.extend(this, Backbone.Events);
      args = _.extend(opts, {
        formBuilder: this
      });
	  
      this.mainView = new BuilderView(args);
    }

    return Formbuilder;

  })();

  window.Formbuilder = Formbuilder;

  if (typeof module !== "undefined" && module !== null) {
    module.exports = Formbuilder;
  } else {
    window.Formbuilder = Formbuilder;
  }

}).call(this);

/* Fields toolbar - samir*/
(function() {
  Formbuilder.registerField('text', {
    order: 0,
    view: "<input <% if (rf.get(Formbuilder.options.mappings.GUIDELINE_PLACEHOLDER)) { %> placeholder='<%=Formbuilder.helpers.simple_format(rf.get(Formbuilder.options.mappings.DESCRIPTION))%>' <% } %> type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />",
    edit: "<%= Formbuilder.templates['edit/size']() %><%= Formbuilder.templates['edit/initial']({rf:rf}) %>",
    addButton: "<span class='symbol'><span class='fa fa-font'></span></span> "+dict['TYPE_TEXT'],
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'small';
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('paragraph', {
    order: 1,
    view: "<textarea <% if (rf.get(Formbuilder.options.mappings.GUIDELINE_PLACEHOLDER)) { %> placeholder='<%=Formbuilder.helpers.simple_format(rf.get(Formbuilder.options.mappings.DESCRIPTION))%>' <% } %> class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>'></textarea>",
    edit: "<%= Formbuilder.templates['edit/size']() %><%= Formbuilder.templates['edit/initial']({rf:rf}) %>",
    addButton: "<span class=\"symbol\">&#182;</span> "+dict['TYPE_TEXTAREA'],
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'small';
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('radio', {
    order: 2,
    view: "<% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n  <div>\n    <label class='fb-option radio-inline'>\n      <input type='radio' <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'checked' %> onclick=\"javascript: return false;\" />\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </label>\n  </div>\n<% } %>\n\n<% if (rf.get(Formbuilder.options.mappings.INCLUDE_OTHER)) { %>\n  <div class='other-option'>\n    <label class='fb-option radio-inline'>\n      <input type='radio' />\n      Other\n    </label>\n\n    <input type='text' />\n  </div>\n<% } %>",
    edit: "<%= Formbuilder.templates['edit/options']({ includeOther: true }) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-circle-o\"></span></span> "+dict['TYPE_RADIO'],
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
		   value: "",
          checked: false
        }, {
          label: "",
		   value: "",
          checked: false
        }
      ];
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('checkboxes', {
    order: 3,
    view: "<% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n  <div>\n    <label class='fb-option'>\n      <input type='checkbox' <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'checked' %> onclick=\"javascript: return false;\" />\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </label>\n  </div>\n<% } %>\n\n<% if (rf.get(Formbuilder.options.mappings.INCLUDE_OTHER)) { %>\n  <div class='other-option'>\n    <label class='fb-option'>\n      <input type='checkbox' />\n      Other\n    </label>\n\n    <input type='text' />\n  </div>\n<% } %>",
    edit: "<%= Formbuilder.templates['edit/options']({ includeOther: true }) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-square-o\"></span></span> "+dict['TYPE_CHECKBOX'],
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
		   value: "",
          checked: false
        }, {
          label: "",
		   value: "",
          checked: false
        }
      ];
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('dropdown', {
    order: 4,
    view: "<select>\n  <% if (rf.get(Formbuilder.options.mappings.INCLUDE_BLANK)) { %>\n    <option value=''>-Select-</option>\n  <% } %>\n\n  <% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n    <option <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'selected' %>>\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </option>\n  <% } %>\n</select>",
    edit: "<%= Formbuilder.templates['edit/options']({ includeBlank: true }) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-caret-down\"></span></span> "+dict['TYPE_SELECT'],
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
		   value: "",
          checked: false
        }, {
          label: "",
		   value: "",
          checked: false
        }
      ];
      attrs.field_options.include_blank_option = false;
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('file', {
    order: 5,
    view: "<input type='file' />",
    edit: "<%= Formbuilder.templates['edit/initial']({rf:rf}) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-file\"></span></span> "+dict['TYPE_FILE']
  });

}).call(this);


(function() {
  Formbuilder.registerField('copy', {
    order: 6,
	type:'non_input',
    view: "<p><%= rf.get(Formbuilder.options.mappings.HTML) %></p>",
    edit: "<%= Formbuilder.templates['edit/tiny_editor']() %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-file-text-o\"></span></span> "+dict['TYPE_PARAGRAPH'],
	defaultAttributes: function(attrs) {
	   attrs.required = false;
       attrs.hide_label = false;
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('email', {
    order: 7,
    view: "<input type='text' <% if (rf.get(Formbuilder.options.mappings.GUIDELINE_PLACEHOLDER)) { %> placeholder='<%=Formbuilder.helpers.simple_format(rf.get(Formbuilder.options.mappings.DESCRIPTION))%>' <% } %> class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />",
    edit: "<%= Formbuilder.templates['edit/size']() %><%= Formbuilder.templates['edit/initial']({rf:rf}) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-envelope-o\"></span></span> "+dict['TYPE_EMAIL'],
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'small';
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('address', {
    order: 8,
    view: "<div class='input-line'>\n  <span class='street'>\n    <input type='text' />\n    <label>"+dict['TEXT_ADDRESS']+"</label>\n  </span>\n</div>\n\n<div class='input-line'>\n  <span class='city'>\n    <input type='text' />\n    <label>"+dict['TEXT_CITY']+"</label>\n  </span>\n\n  <span class='state'>\n    <input type='text' />\n    <label>"+dict['TEXT_STATE']+"</label>\n  </span>\n</div>\n\n<div class='input-line'>\n  <span class='zip'>\n    <input type='text' />\n    <label>"+dict['TEXT_ZIP']+"</label>\n  </span>\n\n  <span class='country'>\n    <select><option>USA</option></select>\n    <label>"+dict['TEXT_COUNTRY']+"</label>\n  </span>\n</div>",
    edit: "",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-home\"></span></span> "+dict['TYPE_ADDRESS']
  });

}).call(this);

(function() {
  Formbuilder.registerField('date', {
    order: 9,
    view: "<div class='input-line'>\n  <span class='month'>\n    <input type=\"text\" />\n    <label>MM</label>\n  </span>\n\n  <span class='above-line'>/</span>\n\n  <span class='day'>\n    <input type=\"text\" />\n    <label>DD</label>\n  </span>\n\n  <span class='above-line'>/</span>\n\n  <span class='year'>\n    <input type=\"text\" />\n    <label>YYYY</label>\n  </span>\n</div>",
    edit: "",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-calendar\"></span></span> "+dict['TYPE_DATE']
  });

}).call(this);

(function() {
  Formbuilder.registerField('time', {
    order: 10,
    view: "<div class='input-line'>\n  <span class='hours'>\n    <input type=\"text\" />\n    <label>HH</label>\n  </span>\n\n  <span class='above-line'>:</span>\n\n  <span class='minutes'>\n    <input type=\"text\" />\n    <label>MM</label>\n  </span>\n\n  <span class='above-line'>:</span>\n\n  <span class='seconds'>\n    <input type=\"text\" />\n    <label>SS</label>\n  </span>\n\n  <span class='am_pm'>\n    <select>\n      <option>AM</option>\n      <option>PM</option>\n    </select>\n  </span>\n</div>",
    edit: "",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-clock-o\"></span></span> "+dict['TYPE_TIME']
  });

}).call(this);


(function() {
  Formbuilder.registerField('submit', {
    order: 12,
	  type:'non_input',
    view: "<% if (rf.get(Formbuilder.options.mappings.RESET_BUTTON)) { %> <input class=\"submit-button\" value=\"Reset\" type=\"button\" /> <% } %><input class=\"submit-button\" value=\"<%= rf.get(Formbuilder.options.mappings.LABEL) %>\" type='button' />",
    edit: "",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-check\"></span></span> "+dict['TYPE_SUBMIT'],
	defaultAttributes: function(attrs) {
	   attrs.required = false;
	   attrs.label = 'Submit';
       attrs.hide_label = true;
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('captcha', {
    order: 13,
	type:'non_input',
    view: "<span class=\"captcha-view\">1 <sub>2</sub> <sup>3</sup> 4</span>",
    edit: "",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-question\"></span></span> "+dict['TYPE_CAPTCHA']
  });

}).call(this);

(function() {
  Formbuilder.registerField('hidden', {
    order: 14,
    type:'non_input',
    view: "<span class=\"hidden-view\"> ---Hidden Element -- </span>",
    edit: "<%= Formbuilder.templates['edit/hidden']() %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-eye-slash\"></span></span> "+dict['TYPE_HIDDEN'],
	defaultAttributes: function(attrs) {
	   attrs.required = false;
	   attrs.label = 'Hidden';
       attrs.hide_label = true;
      return attrs;
    }
  });

}).call(this);

/* Ends of fields regisration */

this["Formbuilder"] = this["Formbuilder"] || {};
this["Formbuilder"]["templates"] = this["Formbuilder"]["templates"] || {};

this["Formbuilder"]["templates"]["edit/base"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p +=
((__t = ( Formbuilder.templates['edit/base_header']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['edit/common']({rf:rf}) )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].edit({rf: rf}) )) == null ? '' : __t) +
'\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/base_header"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-field-label\'>\n  <span data-rv-text="model.' +
((__t = ( Formbuilder.options.mappings.LABEL )) == null ? '' : __t) +
'"></span>\n  <code class=\'field-type\' data-rv-text=\'model.' +
((__t = ( Formbuilder.options.mappings.FIELD_TYPE )) == null ? '' : __t) +
'\'></code>\n  <span class=\'fa fa-arrow-right pull-right\'></span>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/base_non_input"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p +=
((__t = ( Formbuilder.templates['edit/base_header']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['edit/common_non_input']({rf:rf}) )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].edit({rf: rf}) )) == null ? '' : __t) +
'\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/checkboxes"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<label>\n  <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.REQUIRED )) == null ? '' : __t) +
'\' />\n  '+dict['TEXT_REQUIRED']+'\n</label>\n<label>\n  <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.HIDE_LABEL )) == null ? '' : __t) +
'\' />\n  '+dict['TEXT_HIDE_LABEL']+'\n</label>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/checkboxes_non_input"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<label>\n  <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.HIDE_LABEL )) == null ? '' : __t) +
'\' />\n  '+dict['TEXT_HIDE_LABEL']+'\n</label>';

 __t = '<label>\n  <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.RESET_BUTTON )) == null ? '' : __t) +
'\' />\n  '+dict['RESET_BUTTON']+'\n</label>';

if(Formbuilder.helpers.buttonStatus(rf.get(Formbuilder.options.mappings.FIELD_TYPE))) __p +=__t;

}
return __p
};

this["Formbuilder"]["templates"]["edit/common"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>'+dict['TEXT_LABEL']+'</div>\n\n<div class=\'fb-common-wrapper\'>\n  <div class=\'fb-label-description\'>\n    ' +
((__t = ( Formbuilder.templates['edit/label_description']({rf:rf}) )) == null ? '' : __t) +
((__t = ( Formbuilder.templates['edit/label_error']() )) == null ? '' : __t) +
'\n  </div>\n  <div class=\'fb-common-checkboxes\'>\n    ' +
((__t = ( Formbuilder.templates['edit/checkboxes']() )) == null ? '' : __t) +
'\n  </div>\n  <div class=\'fb-clear\'></div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/common_non_input"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>'+dict['TEXT_LABEL']+'</div>\n\n<div class=\'fb-common-wrapper\'>\n  <div class=\'fb-label-description\'>\n    ' +
((__t = ( Formbuilder.templates['edit/label_description_non_input']() )) == null ? '' : __t) +
'\n  </div>\n  <div class=\'fb-common-checkboxes\'>\n    ' +
((__t = ( Formbuilder.templates['edit/checkboxes_non_input']({rf:rf}) )) == null ? '' : __t) +
'\n  </div>\n  <div class=\'fb-clear\'></div>\n</div>\n';

}
return __p
};


this["Formbuilder"]["templates"]["edit/label_description"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<input type=\'text\' data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.LABEL )) == null ? '' : __t) +
'\' />\n<textarea data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.DESCRIPTION )) == null ? '' : __t) +
'\'\n  placeholder=\''+dict['TEXT_GUIDELINE']+'\'></textarea>';

__t = '\n  <label class=\'guidelide-placeholder\'>\n    <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.GUIDELINE_PLACEHOLDER )) == null ? '' : __t) +
'\' />\n    '+dict['TEXT_PLACEHOLDER']+'\n  </label>\n';

if(Formbuilder.helpers.placeholderStatus(rf.get(Formbuilder.options.mappings.FIELD_TYPE))) __p +=__t;


}
return __p
};

this["Formbuilder"]["templates"]["edit/label_error"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<input type="text" data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.ERROR )) == null ? '' : __t) +
'\'\n  placeholder=\''+dict['TEXT_ERROR']+'\'>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/label_description_non_input"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<input type=\'text\' data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.LABEL )) == null ? '' : __t) +
'\' />\n';

}
return __p
};


this["Formbuilder"]["templates"]["edit/tiny_editor"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class="fb-edit-section-header">'+dict['TEXT_COPY_TEXT']+'</div>\n<div class=\'fb-edit-editor\'><textarea data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.HTML )) == null ? '' : __t) +
'\'\n  placeholder=\''+dict['TEXT_COPY_HINT']+'\'></textarea></div>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/hidden"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class="fb-edit-section-header">'+dict['TEXT_VALUE']+'</div>\n<div class=\'fb-edit-hidden\'><input data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.HIDDEN )) == null ? '' : __t) +
'\'\n  placeholder=\''+dict['TEXT_ENTER_VALUE']+'\' /><a title="Show help" style="display: inline-block;font-size: 15px;margin-left: 5px;" onclick="$(this).next(\'abbr\').toggle();" href="javascript:void(0);">?</a><abbr style="display:none"><br />'+dict['TEXT_VALUE_HINT']+'</abbr></div>';

}
return __p
};


this["Formbuilder"]["templates"]["edit/min_max"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>'+dict['TEXT_MAX_MIN']+'</div>\n\nAbove\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MIN )) == null ? '' : __t) +
'" style="width: 30px" />\n\n&nbsp;&nbsp;\n\nBelow\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MAX )) == null ? '' : __t) +
'" style="width: 30px" />\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/min_max_length"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Length Limit</div>\n\nMin\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MINLENGTH )) == null ? '' : __t) +
'" style="width: 30px" />\n\n&nbsp;&nbsp;\n\nMax\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MAXLENGTH )) == null ? '' : __t) +
'" style="width: 30px" />\n\n&nbsp;&nbsp;\n\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.LENGTH_UNITS )) == null ? '' : __t) +
'" style="width: auto;">\n  <option value="characters">characters</option>\n  <option value="words">words</option>\n</select>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/options"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Options</div>\n\n';
 if (typeof includeBlank !== 'undefined'){ ;
__p += '\n  <label class=\'include-blank-label\'>\n    <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INCLUDE_BLANK )) == null ? '' : __t) +
'\' />\n    '+dict['TEXT_INCLUDE_BLANK']+'\n  </label>\n';
 } ;
__p += '\n\n<div class=\'option\' data-rv-each-option=\'model.' +
((__t = ( Formbuilder.options.mappings.OPTIONS )) == null ? '' : __t) +
'\'>\n  <input type="checkbox" class=\'js-default-updated\' data-rv-checked="option:checked" />\n  <input type="text" data-rv-input="option:label" class=\'option-label-input\' />\n  <a class="js-add-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="'+dict['TEXT_ADD_OPTION']+'"><i class=\'fa fa-plus-circle\'></i></a>\n  <a class="js-remove-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="'+dict['TEXT_REMOVE_OPTION']+'"><i class=\'fa fa-minus-circle\'></i></a>\n</div>\n\n';
 if (typeof includeOther !== 'undefined'){ ;
__p += '\n  <label>\n    <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INCLUDE_OTHER )) == null ? '' : __t) +
'\' />\n    '+dict['TEXT_INCLUDE_OTHER']+'\n  </label>\n';
 } ;
__p += '\n\n<div class=\'fb-bottom-add\'>\n  <a class="js-add-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">'+dict['TEXT_ADD_OPTION']+'</a>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/size"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>'+dict['TEXT_SIZE']+'</div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.SIZE )) == null ? '' : __t) +
'">\n  <option value="small">'+dict['TEXT_SIZE_SMALL']+'</option>\n  <option value="medium">'+dict['TEXT_SIZE_MEDIUM']+'</option>\n  <option value="large">'+dict['TEXT_SIZE_LARGE']+'</option>\n</select>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/initial"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {

if(Formbuilder.helpers.initValueStatus(rf.get(Formbuilder.options.mappings.FIELD_TYPE))) {
__p += '\n<div class=\'fb-clear\'></div>\n<div class="fb-edit-section-header">'+dict['TEXT_INITIAL_VAL']+'</div>\n<div class=\'fb-edit-hidden\'><input data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.HIDDEN )) == null ? '' : __t) +
'\'\n  placeholder=\''+dict['TEXT_ENTER_VALUE']+'\' /><a title="Show help" style="display: inline-block;font-size: 15px;margin-left: 5px;" onclick="$(this).next(\'abbr\').toggle();" href="javascript:void(0);">?</a><abbr style="display:none"><br />'+dict['TEXT_VALUE_HINT']+'</abbr></div>';
}
__p += '\n<div class=\'fb-clear\'></div>\n<div class="fb-edit-section-header">'+dict['TEXT_VAL_REGEX']+' </div>\n<div class=\'fb-edit-regex\'><input data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.RGEX )) == null ? '' : __t) +
'\'\n class="regex" placeholder=\''+dict['TEXT_REGEX']+'\' /><a title="Show help" style="display: inline-block;font-size: 15px;margin-left: 5px;" onclick="$(this).next(\'abbr\').toggle();" href="javascript:void(0);">?</a><abbr style="display:none"><br /> '+dict['TEXT_REGEX_HINT']+'</abbr></div>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/units"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>'+dict['TEXT_UNIT']+'</div>\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.UNITS )) == null ? '' : __t) +
'" />\n';

}
return __p
};

this["Formbuilder"]["templates"]["page"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p +=
//((__t = ( Formbuilder.templates['partials/save_button']() )) == null ? '' : __t) +
//'\n' +
((__t = ( Formbuilder.templates['partials/left_side']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['partials/right_side']() )) == null ? '' : __t) +
'\n<div class=\'fb-clear\'></div>';

}
return __p
};

this["Formbuilder"]["templates"]["partials/add_field"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div class=\'fb-tab-pane active\' id=\'addField\'>\n  <div class=\'fb-add-field-types\'>\n    <div class=\'section\'>\n      ';
 _.each(_.sortBy(Formbuilder.inputFields, 'order'), function(f){ ;
__p += '\n        <a data-field-type="' +
((__t = ( f.field_type )) == null ? '' : __t) +
'" class="' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">\n          ' +
((__t = ( f.addButton )) == null ? '' : __t) +
'\n        </a>\n      ';
 }); ;
 _.each(_.sortBy(Formbuilder.nonInputFields, 'order'), function(f){ ;
__p += '\n        <a data-field-type="' +
((__t = ( f.field_type )) == null ? '' : __t) +
'" class="' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">\n          ' +
((__t = ( f.addButton )) == null ? '' : __t) +
'\n        </a>\n      ';
 }); ;
__p += '\n    </div>\n  </div>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["partials/edit_field"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-tab-pane\' id=\'editField\'>\n  <div class=\'fb-edit-field-wrapper\'></div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["partials/left_side"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-left\'>\n  <ul class=\'fb-tabs\'>\n    <li class=\'active\'><a data-target=\'#addField\'>Add new field</a></li>\n    <li><a data-target=\'#editField\'>Field Option</a></li>\n  </ul>\n\n  <div class=\'fb-tab-content\'>\n    ' +
((__t = ( Formbuilder.templates['partials/add_field']() )) == null ? '' : __t) +
'\n    ' +
((__t = ( Formbuilder.templates['partials/edit_field']() )) == null ? '' : __t) +
'\n  </div>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["partials/right_side"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-right\'>\n  <div class=\'fb-no-response-fields\'><i class="fa fa-frown-o"></i> '+dict['TEXT_NO_FIELD']+'</div>\n  <div class=\'fb-response-fields\'></div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["partials/save_button"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-save-wrapper\'>\n  <button class=\'js-save-form ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'\'></button>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["view/base"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'subtemplate-wrapper\'>\n  <div class=\'cover\'></div>\n  ' +
((__t = ( Formbuilder.templates['view/label']({rf: rf}) )) == null ? '' : __t) +
'\n\n  ' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].view({rf: rf}) )) == null ? '' : __t) +
'\n\n  ' +
((__t = ( Formbuilder.templates['view/description']({rf: rf}) )) == null ? '' : __t) +
'\n  ' +
((__t = ( Formbuilder.templates['view/duplicate_remove']({rf: rf}) )) == null ? '' : __t) +
'\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["view/base_non_input"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'subtemplate-wrapper\'>\n  <div class=\'cover\'></div>\n  ' +
((__t = ( Formbuilder.templates['view/label']({rf: rf}) )) == null ? '' : __t) +
'\n\n  ' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].view({rf: rf}) )) == null ? '' : __t) +
'\n\n  ' +
((__t = ( Formbuilder.templates['view/duplicate_remove']({rf: rf}) )) == null ? '' : __t) +
'\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["view/description"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape; 
with (obj) {
__p += '<span class=\'help-block\'>\n  ' +
((__t = ( Formbuilder.helpers.simple_format(rf.get(Formbuilder.options.mappings.DESCRIPTION)) )) == null || rf.get(Formbuilder.options.mappings.GUIDELINE_PLACEHOLDER) ? '' : __t) +
'\n</span>\n';

}
return __p
};

this["Formbuilder"]["templates"]["view/duplicate_remove"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'actions-wrapper\'>\n  <a class="js-duplicate ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Duplicate Field"><i class=\'fa fa-plus-circle\'></i></a>\n  <a class="js-clear ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Remove Field"><i class=\'fa fa-minus-circle\'></i></a>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["view/label"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {

 if (!rf.get(Formbuilder.options.mappings.HIDE_LABEL)) { ;	
__p += '<label>\n  <span>' +
((__t = ( Formbuilder.helpers.simple_format(rf.get(Formbuilder.options.mappings.LABEL)) )) == null ? '' : __t) +
'\n  ';
 if (rf.get(Formbuilder.options.mappings.REQUIRED)) { ;
__p += '\n    <abbr title=\'required\'>*</abbr>\n  ';
 } ;
__p += '\n</label>\n';
 };
}
return __p
};