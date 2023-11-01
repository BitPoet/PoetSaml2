<?php namespace ProcessWire;

class ProcessPoetSaml2 extends Process {
	
	protected static $templateName = 'poetsaml2config';
	
	public static function getModuleInfo() {
		return [
			'title'			=>	__('Poet SAML2 Admin', __FILE__),
			'summary'		=>	__('Management interface for the PoetSaml2 module', __FILE__),
			'version'		=>	'0.0.29',
			'requires'		=>	'PoetSaml2',
			'icon'			=>	'address-book-o',
			'page'			=>	[
				'name'			=>	'poetsaml',
				'parent'		=>	'access',
				'title'			=>	'SAML2 Configuration',
				'icon'			=>	'address-book-o'
			]
		];
	}
	
	
	/**
	 * Output the overview page.
	 *
	 * @return String HTML output
	 */
	public function ___execute() {
		
		$form = $this->buildForm();
		
		return $form->render();
		
	}
	
	
	/**
	 * Add a new profile.
	 *
	 * @return String HTML output
	 */
	public function ___executeAdd() {
		
		$inp = $this->input->post;
		$sanitizer = $this->sanitizer;
		
		$form = $this->buildFormAdd();
		
		if($inp->submit_save) {
			
			$form->processInput($inp);
			$errors = $form->getErrors();
			if($errors) {
				
				foreach($errors as $err) {
					$this->error($err);
				}
				return $form->render();
				
			} else {
				
				$profile = new Page();
				$profile->template = $this->templates->get(self::$templateName);
				$profile->parent = $this->page;
				$profile->name = $sanitizer->pageName($inp->confName);
				$profile->title = $sanitizer->text($inp->confTitle);
				$profile->save();
				
				foreach($profile->template->fields as $f) {
					if($f->type instanceof FieldtypeCheckbox && $f->initValue) {
						$profile->set($f->name, $f->initValue);
					}
				}
				$profile->save();
				
				$this->session->redirect($profile->editUrl);
				
			}
			
		}
		
		return $form->render();
		
	}
	
	
	/**
	 * Export the given profile
	 *
	 * To implement your own exporter, you need to hook before executeExport
	 * and exit PHP at the end of your hook.
	 */
	public function ___executeExport() {
		
		$id = (int)$this->input->get->id;
		
		$exporter = new PagesExportImport();
		
		$name = $this->pages->get($id)->name;
		$ids = $this->pages->find("id=$id, include=all");
		$json = $exporter->exportJSON($ids);
		
		header('Content-Type: application/json');
		header("Content-Disposition: download; filename={$name}.json");
		echo $json;
		exit;
		
	}
	
	
	/**
	 * Create the form for adding a new profile.
	 *
	 * @return InputfieldForm
	 */
	public function ___buildFormAdd() {
		
		$form = $this->modules->get('InputfieldForm');
		$form->attr('method', 'POST');
		$form->attr('action', 'add');
		$form->label = $this->_('Create New SAML2 Profile');
		
		$f = $this->modules->get('InputfieldText');
		$f->attr('id+name', 'confName');
		$f->label = $this->_('Profile Name');
		$f->description = $this->_('Lowercase letters, digits and underscores are allowed. Must start with a lowercase letter and not end with an underscore');
		$f->required = true;
		$f->attr('pattern', '[a-z]([a-z0-9_]*[a-z0-9])*');
		$form->append($f);
		
		$f = $this->modules->get('InputfieldText');
		$f->attr('id+name', 'confTitle');
		$f->label = $this->_('Label');
		$f->description = $this->_('Readable Label for the Profile');
		$f->required = true;
		$form->append($f);
		
		$f = $this->modules->get('InputfieldSubmit');
		$f->attr('id+name', 'submit_save');
		$f->attr('value', $this->_('Create Profile'));
		$form->append($f);

		return $form;
		
	}
	
	/**
	 * Create the admin overview form for managing Saml2 profiles
	 *
	 * @return InputfieldForm
	 */
	public function ___buildForm() {
		
		$form = $this->modules->get('InputfieldForm');
		$form->label = $this->_('Configure SAML2 IdPs');
		
		$scss = $this->config->sessionCookieSameSite;
		if($scss !== 'None') {
			$this->error($this->_('$config->sessionCookieSameSite must be set to "None" for SAML2 authentication to work!'));
		}
		
		$profilePages = $this->page->children('template=' . self::$templateName);
		
		if($profilePages->count() > 0) {
			
			$mrk = $this->modules->get('MarkupAdminDataTable');
			$mrk->setEncodeEntities(false);
			$mrk->headerRow([
				$this->_('Active'),
				$this->_('Profile'),
				$this->_('SP'),
				$this->_('IdP'),
				$this->_('Metadata'),
				$this->_('Backup'),
				$this->_('Delete')
			]);
			
			foreach($profilePages as $profile) {
				
				$urlBase = $this->modules->get('PoetSaml2')->urlBase;
				$metadataUrl = ($this->config->https? 'https' : 'http') . '://' . $this->config->httpHost . $this->config->urls->root . $urlBase . '/' . $profile->name . '/metadata?download=1';
				$disabled = $profile->ps2Active ? '' : "disabled='disabled'";
				$activeIcon = $profile->ps2Active ? 'fa-check-square-o' : 'fa-square-o';
				
				$mrk->row([
					"<i class='fa $activeIcon'> </i>",
					'<a href="' . $profile->editUrl . '"><i class="fa fa-edit"> </i> ' . $profile->title . '</a>',
					$this->sanitizer->truncate($profile->ps2OurEntityId, 40),
					$this->sanitizer->truncate($profile->ps2IdpEntityId, 40),
					"<a class='fa fa-download' href='$metadataUrl' title='" . $this->_("Download Metadata XML") . "' $disabled> </a>",
					"<a class='fa fa-floppy-o' href='./export?id={$profile->id}' title='" . $this->_("Export Profile") . "'> </a>",
					"<a class='fa fa-trash' href='del?id={$profile->id}' title='" . $this->_("Delete Profile") . "'> </a>"
				]);
			}
			
			$wrap = $this->modules->get('InputfieldMarkup');
			$wrap->attr('value', $mrk->render());
			$form->append($wrap);
			
		} else {
			
			$wrap = $this->modules->get('InputfieldMarkup');
			$wrap->attr('value', '<p class="ps2-noconfigs">' . $this->_('No SAML2 profiles found') . '</p>');
			$form->append($wrap);
			
		}
		
		$f = $this->modules->get('InputfieldButton');
		$f->attr('name', 'btnAdd');
		$f->attr('href', 'add');
		$f->html = '<i class="fa fa-plus-o"> </i> ' . $this->_('Add Profile');
		$form->append($f);
		
		return $form;
		
	}
	
	
	public function ___install() {
		
		$this->createIndividualFields();
		$this->createTemplate();
		$this->createRepeaters();
		
		parent::___install();
		
	}
	
	
	public function ___uninstall() {
		
		$this->removeEndpointsPages();
		$this->removeRepeaters();
		$this->removeTemplate();
		$this->removeIndividualFields();
		
		parent::___uninstall();

	}
	
	
	public function ___upgrade($from, $to) {
		
		if(version_compare($from, '0.0.27', '<')) {

			$fieldDefsAdv = include($this->definitionPath('advFields.php'));
			$this->createFields($fieldDefsAdv);
			
			$template = $this->templates->get(self::$templateName);
			$fg = $template->fieldgroup;
			
			$this->addFieldsToFieldgroup($fg, $fieldDefsAdv);
			
		}
		
		if(version_compare($from, '0.0.27', '=')) {

			$fields = include($this->definitionPath('fields.php'));
			$this->createFields(['ps2BackendButton' => $fields['ps2BackendButton']]);
			
			$template = $this->templates->get(self::$templateName);
			$fg = $template->fieldgroup;
			$fButton = $this->fields->get('ps2BackendButton');
			$fActive = $this->fields->get('ps2Active');
			$fg->insertAfter($fButton, $fActive);
			$fg->save();

		}
	}
	
	
	protected function definitionPath($file) {
		return __DIR__ . DIRECTORY_SEPARATOR . 'defs' . DIRECTORY_SEPARATOR . $file;
	}
	
	protected function createIndividualFields() {
		
		$fieldDefs = include($this->definitionPath('fields.php'));
		$fieldDefsAdv = include($this->definitionPath('advFields.php'));
		
		$fieldDefs = array_merge($fieldDefs, $fieldDefsAdv);
		
		$this->createFields($fieldDefs);
	}
	
	
	protected function createFields($fieldDefs) {

		foreach($fieldDefs as $fname => $fdata) {

			$f = $this->fields->get($fname);
			if(! $f) {

				$f = new Field();
				$f->name = $fname;
				$f->setImportData($fdata);
				$f->save();
				
				if($fdata['type'] === 'options' && isset($fdata['export_options'])) {
					$optString = str_replace("\\n", chr(10), $fdata['export_options']['default']);
					$optLines = explode(chr(10), $optString);
					$mgr = new SelectableOptionManager();
					$opts = new SelectableOptionArray();
					foreach($optLines as $optLine) {
						list($num, $txt) = explode('=', $optLine, 2);
						list($value, $label) = explode('|', $txt, 2);
						$this->log(sprintf("Adding option %d to field %s: value = '%s', label = '%s'", $num, $fname, $value, $label));
						$opt = new SelectableOption();
						$opt->title = $label;
						$opt->value = $value;
						$opts->add($opt);
					}
					$mgr->setOptions($f, $opts, false);
				}
				
				$f->addFlag(Field::flagSystem);
				$this->fields->save($f);

			}

		}

	}
	
	
	protected function parseOptions($str, $forField) {
		$lines = explode('\\n', $str);
		$opts = new SelectableOptionArray();
		$opts->setField($forField);
		foreach($lines as $line) {
			list($id, $value) = explode('=', $line, 2);
			$opt = new SelectableOption();
			$opt->set('id', $id);
			$opt->set('sort', $id);
			$opt->set('value', $value);
			$opt->set('title', $value);
		}
		return $opts;
	}
	
	protected function createRepeaters() {

		$repDef = include($this->definitionPath('repeater.php'));
		foreach($repDef['fields'] as $fname => $fdata) {
			$f = $this->fields->get($fname);
			if(! $f) {
				$f = new Field();
				$f->name = $fname;
				$f->setImportData($fdata);
				$f->addFlag(Field::flagSystem);
				$this->fields->save($f);
			}
		}
		
		$after = $repDef['after'];
		$repData = $repDef['this'];
		$f_name = $repData['name'];
		
		$fg = new Fieldgroup;
		$fg->name = "repeater_" . $f_name;
		foreach($repData['repeaterFields'] as $rf) {
			$fg->append(wire('fields')->get($rf));
		}
		$fg->save();
		
		$tpl = new Template;
		$tpl->name = "repeater_" . $f_name;
		$tpl->fieldgroup = $fg;
		$tpl->flags = Template::flagSystem;
		$tpl->noChildren = 1;
		$tpl->noParents = 1;
		$tpl->noGlobal = 1;
		$tpl->flags = 'PoetSaml2';
		$tpl->save();
		
		$f = new Field;
		$f->type = wire('modules')->get('FieldtypeRepeater');
		$f->name = $f_name;
		//$f->parent_id = wire('pages')->get("name=for-field-{$f->id}")->id;
		$f->repeaterTitle = $repData['repeaterTitle'];
		$f->template_id = $tpl->id;
		$f->label = $repData['label'];
		$f->addFlag(Field::flagSystem);
		$f->save();
		
		$afterElement = $this->fields->get($after);
		
		$fg = $this->templates->get(self::$templateName)->fieldgroup;
		$fg->insertAfter($f, $afterElement);
		$fg->save();
		
	}
	
	protected function createTemplate() {
		
		$template = $this->templates->get(self::$templateName);

		if($template)
			return;

		// Create the template for SP profiles
		$template = $this->templates->add(self::$templateName, [
			'tags'			=>	'PoetSaml2',
			'label'			=>	'PoetSaml2 SP',
			'flags'			=>	Template::flagSystem
		]);
		
		// Add all fields to our template
		$fg = $template->fieldgroup;
		
		$fieldDefs = include($this->definitionPath('fields.php'));
		$fieldDefsAdv = include($this->definitionPath('advFields.php'));
		$fieldDefs = array_merge($fieldDefs, $fieldDefsAdv);

		$this->addFieldsToFieldgroup($fg, $fieldDefs, true);
	}
	
	
	protected function addFieldsToFieldgroup($fg, $fieldDefs, $addTitle = false) {
		
		$fieldDefs = include($this->definitionPath('fields.php'));
		$fieldDefsAdv = include($this->definitionPath('advFields.php'));
		$fieldDefs = array_merge($fieldDefs, $fieldDefsAdv);

		if($addTitle)		
			$fg->add($this->fields->get('title'));
		
		foreach($fieldDefs as $fname => $fdata) {
			$fg->add($this->fields->get($fname));
		}
		$fg->save();
	}
	
	
	protected function removeEndpointsPages() {
		
		$ps = $this->pages->find("template=" . self::$templateName . ", include=all");
		foreach($ps as $p) {
			$this->pages->delete($p);
		}
		
	}
	
	protected function removeTemplate() {
		
		$tpl = $this->templates->get(self::$templateName);
		if(! $tpl)
			return;
		
		$tpl->setFlags($tpl->get('flags') | Template::flagSystemOverride);
		$tpl->setFlags($tpl->get('flags') ^ Template::flagSystem);
		$this->templates->delete($tpl);
		
	}
	
	protected function removeRepeaters() {

		$repDef = include($this->definitionPath('repeater.php'));

		$repData = $repDef['this'];
		$f = $this->fields->get($repData['name']);
		
		$fg = $this->fieldgroups->get('poetsaml2config');
		if($fg) {
			if($f)
				$fg->remove($f);
			$fg->save();
		}
		
		if($f) {
			$f->addFlag(Field::flagSystemOverride);
			$f->removeFlag(Field::flagSystem);
			$this->fields->delete($f);
		}
		
		$fieldNames = array_reverse(array_keys($repDef['fields']));
		
		foreach($fieldNames as $k) {
			$f = $this->fields->get($k);
			if($f) {
				$f->addFlag(Field::flagSystemOverride);
				$f->removeFlag(Field::flagSystem);
				$this->fields->delete($f);
			}
		}
		
	}
	
	protected function removeIndividualFields() {

		$fieldDefs = include($this->definitionPath('fields.php'));
		$fieldDefsAdv = include($this->definitionPath('advFields.php'));
		
		$fieldDefs = array_merge($fieldDefs, $fieldDefsAdv);

		foreach($fieldDefs as $fname => $fdata) {
			$f = $this->fields->get($fname);
			if($f) {
				$f->addFlag(Field::flagSystemOverride);
				$f->removeFlag(Field::flagSystem);
				$this->fields->delete($f);
			}
		}
		
	}
	
}