dojo.provide('dope.entity.FormContainer');
dojo.require('dope.entity.Form');
dojo.require('dope.layout.BorderContainer');

dojo.declare('dope.entity.FormContainer', [dope.layout.BorderContainer, dope.entity.Form], {
	baseClass: 'dopeForm'
});