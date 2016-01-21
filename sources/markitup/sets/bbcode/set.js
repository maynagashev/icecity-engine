// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
myBbcodeSettings = {
	previewParserPath:	'', // path to your BBCode parser
	markupSet: [
		{name:'Bold', className:"btn-b", key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name:'Italic', className:"btn-i", key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name:'Underline', className:"btn-u", key:'U', openWith:'[u]', closeWith:'[/u]'},		
		{separator:'---------------' },
		
		{name:'Picture', className:"btn-picture", key:'P', replaceWith:'[img][![Url]!][/img]'},
		{name:'Link', className:"btn-link", key:'L', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...'},
		{separator:'---------------' },
		
		{name:'Size', className:"btn-size", key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]',
  		dropMenu :[
  			{name:'Big', openWith:'[size=200]', closeWith:'[/size]' },
  			{name:'Normal', openWith:'[size=100]', closeWith:'[/size]' },
  			{name:'Small', openWith:'[size=50]', closeWith:'[/size]' }
  		]},
		{separator:'---------------' },
		
		{name:'Bulleted list', className:"btn-bul-list", openWith:'[list]\n', closeWith:'\n[/list]'},
		{name:'Numeric list', className:"btn-num-list", openWith:'[list=[![Starting number]!]]\n', closeWith:'\n[/list]'}, 
		{name:'List item', className:"btn-list-item", openWith:'[*] '},
		{separator:'---------------' },
		
		{name:'Quotes', className:"btn-quotes", openWith:'[quote]', closeWith:'[/quote]'},
		{name:'Code', className:"btn-code", openWith:'[code]', closeWith:'[/code]'}, 
		{separator:'---------------' },
		
		{name:'Clean', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		{	name:'Colors', 
			className:'colors', 
			openWith:'[color=[![Color]!]]', 
			closeWith:'[/color]', 
				dropMenu: [
					{name:'Yellow',	openWith:'[color=yellow]', 	closeWith:'[/color]', className:"col1-1" },
					{name:'Orange',	openWith:'[color=orange]', 	closeWith:'[/color]', className:"col1-2" },
					{name:'Red', 	openWith:'[color=red]', 	closeWith:'[/color]', className:"col1-3" },
					
					{name:'Blue', 	openWith:'[color=blue]', 	closeWith:'[/color]', className:"col2-1" },
					{name:'Purple', openWith:'[color=purple]', 	closeWith:'[/color]', className:"col2-2" },
					{name:'Green', 	openWith:'[color=green]', 	closeWith:'[/color]', className:"col2-3" },
					
					{name:'White', 	openWith:'[color=white]', 	closeWith:'[/color]', className:"col3-1" },
					{name:'Gray', 	openWith:'[color=gray]', 	closeWith:'[/color]', className:"col3-2" },
					{name:'Black',	openWith:'[color=black]', 	closeWith:'[/color]', className:"col3-3" }
				]
		}
		
	
	]
}

myBbcodeSettingsLight = {
	previewParserPath:	'', // path to your BBCode parser
	markupSet: [
		{name:'Bold', className:"btn-b", key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name:'Italic', className:"btn-i", key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name:'Underline', className:"btn-u", key:'U', openWith:'[u]', closeWith:'[/u]'},		
		{separator:'---------------' },
		
		{name:'Link', className:"btn-link", key:'L', openWith:'[url=[![Url]!]]', closeWith:'[/url]', placeHolder:'Your text to link here...'},
		{separator:'---------------' },
		
		{name:'Size', className:"btn-size", key:'S', openWith:'[size=[![Text size]!]]', closeWith:'[/size]',
		dropMenu :[
			{name:'Big', openWith:'[size=200]', closeWith:'[/size]' },
			{name:'Normal', openWith:'[size=100]', closeWith:'[/size]' },
			{name:'Small', openWith:'[size=50]', closeWith:'[/size]' }
		]},
		{separator:'---------------' },
		
		{name:'Bulleted list', className:"btn-bul-list", openWith:'[list]\n', closeWith:'\n[/list]'},
		{name:'Numeric list', className:"btn-num-list", openWith:'[list=[![Starting number]!]]\n', closeWith:'\n[/list]'}, 
		{name:'List item', className:"btn-list-item", openWith:'[*] '},
		{separator:'---------------' },
		
		{name:'Quotes', className:"btn-quotes", openWith:'[quote]', closeWith:'[/quote]'},
		{name:'Code', className:"btn-code", openWith:'[code]', closeWith:'[/code]'}, 
		{separator:'---------------' },
		{name:'Clean', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } }
	
	]
}