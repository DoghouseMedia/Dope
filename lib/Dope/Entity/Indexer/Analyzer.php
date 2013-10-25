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
		'better',
		'bit',
		'both',
		'but',
		'by',
		'c',
		'came',
		'can',
		'capable',
		'could',
		'course',
		'd',
		'decided',
		'did',
		'didn',
		'different',
		'div',
		'doesn',
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
		'have',
		'her',
		'here',
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
		'like',
		'll',
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
		'probably',
		'problem',
		'put',
		'quite',
		'quot',
		'r',
		'really',
		'results',
		'right',
		's',
		'same',
		'saw',
		'set',
		'several',
		'she',
		'should',
		'since',
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
		'who',
		'with',
		'would',
		'yet',
		'you',
		'your',
		'yours'
	);
	
	public static function analyze($text, $encoding=null, $forceUseStopwords=true, $keepIndexes=false, $allowAsterisks=false, $allowLeadingSigns=false)
	{
	    /* Strip tags */
	    $text = preg_replace('/(<[^<>]*?>)+/', ' ', $text);
	    
	    /* Strip special characters */
		$text = preg_replace('/[\'`�"]/', '', $text);
		
		/* Strip accents */
		$text = static::unaccent($text);
	
		/* Normalize */
		
		if (preg_match('/' . \Dope_Form_Element_Phone::REGEXP . '/is', $text)) {
			$text = preg_replace('/[^0-9]/mis','',$text);
		}
		elseif (preg_match('/' . \Dope_Form_Element_Mobile::REGEXP . '/is', $text)) {
			$text = preg_replace('/[^0-9]/mis','',$text);
		}
		elseif (preg_match('/' . \Dope_Form_Element_Email::REGEXP . '/is', $text)) {
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
		$ascii = @iconv('UTF-8', 'ASCII//TRANSLIT', $text);
		if (!$ascii) {
		    $ascii = @iconv('UTF-8', 'ASCII//IGNORE', $text);
		}
		return preg_replace(array('/[^\w*]/', '/-+/'), array('-', '-'), strtolower($ascii));
	}
}