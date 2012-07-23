<?php

namespace Dope\Entity\Indexer;

require_once 'Dope/Form/Element/Phone.php';
require_once 'Dope/Form/Element/Email.php';
require_once 'Dope/Form/Element/Mobile.php';

class Analyzer
{
	protected static $_stopwords = array(
		// Ticket #1787. Fixed searchable behavior numeric evaluation
		// Removed the numeric 0-9 from here
		'a',
		'about',
		'after',
		'all',
		'almost',
		'along',
		'also',
		'although',
		'amp',
		'an',
		'and',
		'another',
		'any',
		'are',
		'area',
		'arent',
		'around',
		'as',
		'at',
		'available',
		'back',
		'be',
		'because',
		'been',
		'before',
		'being',
		'best',
		'better',
		'big',
		'bit',
		'both',
		'but',
		'by',
		'c',
		'came',
		'can',
		'capable',
		'control',
		'could',
		'course',
		'd',
		'dan',
		'day',
		'decided',
		'did',
		'didn',
		'different',
		'div',
		'do',
		'doesn',
		'don',
		'down',
		'drive',
		'e',
		'each',
		'easily',
		'easy',
		'edition',
		'either',
		'end',
		'enough',
		'even',
		'every',
		'example',
		'few',
		'find',
		'first',
		'for',
		'found',
		'from',
		'get',
		'go',
		'going',
		'good',
		'got',
		'gt',
		'had',
		'hard',
		'has',
		'have',
		'he',
		'her',
		'here',
		'how',
		'i',
		'if',
		'in',
		'into',
		'is',
		'isn',
		'it',
		'just',
		'know',
		'last',
		'left',
		'li',
		'like',
		'little',
		'll',
		'long',
		'look',
		'lot',
		'lt',
		'm',
		'made',
		'make',
		'many',
		'mb',
		'me',
		'menu',
		'might',
		'mm',
		'more',
		'most',
		'much',
		'my',
		'name',
		'nbsp',
		'need',
		'new',
		'no',
		'not',
		'now',
		'number',
		'of',
		'off',
		'old',
		'on',
		'one',
		'only',
		'or',
		'original',
		'other',
		'our',
		'out',
		'over',
		'part',
		'place',
		'point',
		'pretty',
		'probably',
		'problem',
		'put',
		'quite',
		'quot',
		'r',
		're',
		'really',
		'results',
		'right',
		's',
		'same',
		'saw',
		'see',
		'set',
		'several',
		'she',
		'sherree',
		'should',
		'since',
		'size',
		'small',
		'so',
		'some',
		'something',
		'special',
		'still',
		'stuff',
		'such',
		'sure',
		'system',
		't',
		'take',
		'than',
		'that',
		'the',
		'their',
		'them',
		'then',
		'there',
		'these',
		'they',
		'thing',
		'things',
		'think',
		'this',
		'those',
		'though',
		'through',
		'time',
		'to',
		'today',
		'together',
		'too',
		'took',
		'two',
		'up',
		'us',
		'use',
		'used',
		'using',
		've',
		'very',
		'want',
		'was',
		'way',
		'we',
		'well',
		'went',
		'were',
		'what',
		'when',
		'where',
		'which',
		'while',
		'white',
		'who',
		'will',
		'with',
		'would',
		'yet',
		'you',
		'your',
		'yours'
	);
	
	public static function analyze($text, $encoding=null, $forceUseStopwords=false, $keepIndexes=true, $allowAsterisks=false, $allowLeadingSigns=false)
	{
		$text = preg_replace('/[\'`�"]/', '', $text);
		$text = static::unaccent($text);
	
		/* Normalize */
		
		if (preg_match('/' . \Dope_Form_Element_Phone::REGEXP . '/mis', $text)) {
			$text = preg_replace('/[^0-9]/mis','',$text);
		}
		elseif (preg_match('/' . \Dope_Form_Element_Mobile::REGEXP . '/mis', $text)) {
			$text = preg_replace('/[^0-9]/mis','',$text);
		}
		elseif (preg_match('/' . \Dope_Form_Element_Email::REGEXP . '/mis', $text)) {
			$text = preg_replace('/[^A-Za-z0-9]/mis','',$text);
		}
		else {
			$patterns = array();
			$replacements = array();
			 
			if ($allowLeadingSigns) {
				$patterns[] = '/([A-Za-z0-9])[\+\-]([A-Za-z0-9])/';
				$replacements[] = '$1 $2';
			} else {
				$patterns[] = '/[\+\-]/';
				$replacements[] = ' ';
			}
	
			$patterns[] = $allowAsterisks ? '/[^A-Za-z0-9\+\-\*]/' : '/[^A-Za-z0-9\+\-]/';
			$replacements[] = ' ';
	
			$text = preg_replace($patterns, $replacements, $text);
		}
	
		$text = str_replace('  ', ' ', $text);
	
		$terms = explode(' ', $text);
	
		$ret = array();
		if (! empty($terms)) {
			foreach ($terms as $i => $term) {
				if (empty($term)) {
					continue;
				}
				$lower = strtolower(trim($term));
	
				if (($forceUseStopwords OR count($terms) > 5)) {
					if (in_array($lower, self::$_stopwords)) {
						continue;
					}
				}
	
				$ret[$i] = $lower;
			}
		}
	
		return $keepIndexes ? $ret : array_values($ret);
	}
	
	public static function unaccent($text)
	{
		//setlocale(LC_CTYPE, 'en_US.utf8');
		//$text = "Babí léto definitivně skončilo, zatáhne se a na horách začne sněžit";
		$ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
		return preg_replace(array('/\W/', '/-+/'), array('-', '-'), strtolower($ascii));
	}
}