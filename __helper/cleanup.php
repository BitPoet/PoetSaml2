<?php namespace ProcessWire;

include('../../../../index.php');

foreach($pages->find('template=poetsaml2config, include=all') as $p) {
	$pages->delete($p, true);
}

foreach($templates as $tpl) {

	if(strpos($tpl->name, 'repeater_') !== 0 && $tpl->tags === 'PoetSaml2') {
		echo "Template " . $tpl->name . '...';
		$tpl->setFlags($tpl->get('flags') | Template::flagSystemOverride);
		$tpl->setFlags($tpl->get('flags') ^ Template::flagSystem);
		$templates->delete($tpl);
		echo "[DELETED]" . PHP_EOL;
	}
	
}

foreach($fields as $f) {

	if($f->type instanceof FieldtypeRepeater && ($f->tags === 'poetsaml2' || strpos($f->name, 'ps2') === 0)) {
		echo "Repeater field " . $f->name . "...";
		$f->addFlag(Field::flagSystemOverride);
		$f->removeFlag(Field::flagSystem);
		$fields->delete($f);
		echo "[DELETED]" . PHP_EOL;
	}
	
}

foreach($templates as $tpl) {

	if(strpos($tpl->name, 'repeater_ps2') === 0) {
		echo "Template " . $tpl->name . '...';
		$tpl->setFlags($tpl->get('flags') | Template::flagSystemOverride);
		$tpl->setFlags($tpl->get('flags') ^ Template::flagSystem);
		$tpl->save();
		$templates->delete($tpl);
		echo "[DELETED]" . PHP_EOL;
	}
	
}

foreach($fields as $f) {

	if($f->tags === 'poetsaml2' || $f->tags === 'poetsaml2') {
		echo "Repeater field " . $f->name . "...";
		$f->addFlag(Field::flagSystemOverride);
		$f->removeFlag(Field::flagSystem);
		$fields->delete($f);
		echo "[DELETED]" . PHP_EOL;
	}
	
}

foreach($fieldgroups as $fg) {
	if(strpos($fg->name, 'repeater_ps2') === 0) {
		echo "Fieldgroup " . $fg->name . '...';
		$fieldgroups->delete($fg);
		echo "[DELETED]" . PHP_EOL;
	}
}