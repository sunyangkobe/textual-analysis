<?php
/**
 * 2011 Aug 05
 * CSC309 - Textual Analysis
 *
 * stringProcessor class
 * Multiple string operations are performed here
 *
 * @author Kobe Sun
 *
 */

class strProcessor {

	private $content;
	private $topk;

	private $numSentences;
	private $numWords;
	private $numUniqWords;
	private $numEnChar;
	private $freqChar;
	private $freqUniqWords;
	private $freqTopWords;


	/**
	 *
	 * All function in this class will be applied on $content, case insensitive.
	 * $topk will be used to determine the number of most frequent words.
	 * @param String $content
	 * @param int $topk
	 */
	public function __construct($content, $topk) {
		$this->content = $content;
		$this->topk = $topk;

		// for efficiency, keep the reference to the results
		$this->numSentences = $this->countSentences();
		$this->numWords = $this->countWords();
		$this->numUniqWords = $this->countUniqueWords();
		$this->numEnChar = $this->countEnglishChar();
		$this->freqChar = $this->englishCharFreq();
		$this->freqUniqWords = $this->uniqueWordsFreq();
		$this->freqTopWords = $this->topKUniqueWords();
	}


	/**
	 *
	 * Count the number of sentences in the text, ("."|"!"|"?"|"~") will be
	 * considered as terminators. Consequent terminators will be treated as
	 * one terminator only.
	 * @return int the number of sentences in the text
	 */
	private function countSentences() {
		$prev = -2;
		$count = 0;
		$ending_chars = array(".", "!", "?", "~");
		$char_arr = str_split($this->content);
		for ($i = 0; $i < count($char_arr); $i ++) {
			if ( ($prev != $i - 1) && in_array($char_arr[$i], $ending_chars) ) {
				$count ++;
			}

			if (in_array($char_arr[$i], $ending_chars)) {
				$prev = $i;
			} elseif (($i == count($char_arr) - 1) && !empty($char_arr[$i])) {
				$count ++;
			}
		}
		return $count;
	}


	/**
	 *
	 * Count the number of words in the text
	 * @return int the number of words in the text
	 */
	private function countWords() {
		return str_word_count($this->content);
	}


	/**
	 *
	 * Count the number of unique words in the text
	 * @return int number of unique words in the text
	 */
	private function countUniqueWords() {
		$str_arr = str_word_count($this->content, 1);
		$str_arr = array_unique($str_arr);
		return count($str_arr);
	}


	/**
	 *
	 * Count the number of English characters in the text
	 * @return int number of English characters in the text
	 */
	private function countEnglishChar() {
		$char_arr = str_split($this->content);
		$count = 0;
		foreach ($char_arr as $char) {
			if (preg_match("/^[a-z]$/i", $char)) {
				$count ++;
			}
		}
		return $count;
	}


	/**
	 *
	 * Gather data and generate the frequency table for english characters.
	 * @return array frequency table
	 */
	private function englishCharFreq(){
		$freq_tbl = array();
		$char_arr = str_split($this->content);
		foreach ($char_arr as $k) {
			if (preg_match("/^[a-z]$/i", $k)) {
				array_key_exists($k, $freq_tbl) ? $freq_tbl[$k]++ : $freq_tbl[$k] = 1;
			}
		}
		ksort($freq_tbl);
		return $freq_tbl;
	}


	/**
	 *
	 * Gather data and generate the frequency table for unique words.
	 * @return array frequency table
	 */
	private function uniqueWordsFreq(){
		$freq_tbl = array();
		$str_arr = str_word_count($this->content, 1);
		foreach ($str_arr as $k) {
			if (preg_match("/^[a-z]+$/i", $k)) {
				array_key_exists($k, $freq_tbl) ? $freq_tbl[$k]++ : $freq_tbl[$k] = 1;
			}
		}
		return $freq_tbl;
	}


	/**
	 *
	 * Gather data and generate the table that reflect the most frequent words
	 * in the text, according to $topk.
	 * @return array most frequent words
	 */
	private function topKUniqueWords() {
		if (empty($this->freqUniqWords)) {
			$this->freqUniqWords = array_slice($this->uniqueWordsFreq(), 0, $this->topk);
		}
		arsort($this->freqUniqWords);
		return array_slice($this->freqUniqWords, 0, $this->topk);
	}


	/**
	 *
	 * Convert the common frequency table format to JSON-based properties.
	 * @param array $arr
	 * @return array transformed array
	 * @example
	 * ["a" => 2] will be transformed to ["name" => "a", "freq" => 2]
	 */
	private function freqTbl2JSON($arr) {
		$result = array();
		foreach ($arr as $k => $v) {
			array_push($result, array("name" => $k, "freq" => $v));
		}
		return $result;
	}


	/**
	 *
	 * Gather data and return the class object as an array
	 * @return array representation of the class object
	 */
	public function gatherStats() {
		return array (
			"num_sentences" => $this->numSentences,
			"num_words" => $this->numWords,
			"num_uniq_words" => $this->numUniqWords,
			"num_chars" => $this->numEnChar,
			"freq_tbl" => $this->freqTbl2JSON($this->freqChar),
			"most_freq" => $this->freqTbl2JSON($this->freqTopWords)
		);
	}


	/**
	 *
	 * For efficiency, it doesn't make sense to recalculate all fields when
	 * combining the data, simply adding up the result. Change nothing if
	 * the input type is incorrect.
	 * @param strProcessor $obj
	 */
	public function combineData($obj) {
		if ($obj instanceof strProcessor) {
			$this->content .= $obj->getContent() . "\n";
			$this->numSentences += $obj->getNumSentences();
			$this->numWords += $obj->getNumWords();
			$this->numUniqWords += $obj->getNumUniqWords();
			$this->numEnChar += $obj->getNumEnChar();
			$this->combineFreqTbl($this->freqChar, $obj->getFreqChar());
			$this->combineFreqTbl($this->freqUniqWords, $obj->getFreqUniqWords());
			$this->freqTopWords = $this->topKUniqueWords();
		}
	}


	/**
	 *
	 * Combine 2 frequenct table
	 * @param array ref &$refSelf
	 * @param array ref &$refNew
	 */
	private function combineFreqTbl(&$refSelf, &$refNew){
		foreach ($refNew as $k=>$v) {
			if (array_key_exists($k, $refSelf)) $refSelf[$k] += $v;
			else $refSelf[$k] = $v;
		}
		ksort($refSelf);
	}


	public function getContent() { return $this->content; }
	public function getTopk() { return $this->topk; }
	public function getNumSentences() { return $this->numSentences; }
	public function getNumWords() { return $this->numWords; }
	public function getNumUniqWords() { return $this->numUniqWords; }
	public function getNumEnChar() { return $this->numEnChar; }
	public function getFreqChar() { return $this->freqChar; }
	public function getFreqUniqWords() { return $this->freqUniqWords; }
	public function getFreqTopWords() { return $this->freqTopWords; }
	public function setContent($x) { $this->content = $x; }
	public function setTopk($x) { $this->topk = $x; }
	public function setNumSentences($x) { $this->numSentences = $x; }
	public function setNumWords($x) { $this->numWords = $x; }
	public function setNumUniqWords($x) { $this->numUniqWords = $x; }
	public function setNumEnChar($x) { $this->numEnChar = $x; }
	public function setFreqChar($x) { $this->freqChar = $x; }
	public function setFreqUniqWords($x) { $this->freqUniqWords = $x; }
	public function setFreqTopWords($x) { $this->freqTopWords = $x; }

}