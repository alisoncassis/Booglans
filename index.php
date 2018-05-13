<?php
  $text = require_once("./textB.php");
  // $text = require_once("./textA.php");

  function countBooglansPrepositions($word, $nonPrepositionWords, $count) {
    //preposições tem 5 letras, terminando em uma do tipo 'bar' porém não podem conter 'l' na palavra

    $hasL = strpos($word, "l");
    if(strlen($word) === 5 && $hasL === false && !in_array($word[4], $nonPrepositionWords)) {
      return ++$count;
    } else {
      return $count;
    }
  };

  function countBooglansVerbsAndPersons($word, $nonPrepositionWords, $verb) {
    //verbo tem 8 ou mais letras, terminando em uma do tipo 'bar'
    //se o verbo começa com uma letra do tipo 'bar' ele está em primeira pessoa

    if(strlen($word) >= 8 && !in_array($word[strlen($word) - 1], $nonPrepositionWords)) {
      $verb["count"] = ++$verb["count"];
      if(!in_array($word[0], $nonPrepositionWords)) {
        $verb["firstPersons"] = ++$verb["firstPersons"];
      }
    }
    return $verb;
  };

  function countBooglansBeautifullNumbers($word, $alphabet, $beautifullNumbers) {
    $value = 0;
    foreach (str_split($word) as $index => $caractere) {
      $value = $value + (array_search($caractere, $alphabet) * (20 ** $index));
    }
    if($value >= 422224 && $value % 3 === 0) {
      if(!in_array($value, $beautifullNumbers['numbers'])) {
        $beautifullNumbers['numbers'][] = $value;
        ++$beautifullNumbers['count'];
      }
    }
    return $beautifullNumbers;
  }

  function compareByBooglansAlphabet($currentWord, $nextWord) {
    $alphabet = require('./alphabet.php');
    $currentWordLength = strlen($currentWord);
    $nextWordLength = strlen($nextWord);
    $minLength = min($currentWordLength, $nextWordLength);

    for ($i = 1; $i < $minLength; $i++) {
      $currentWordCaractere = $currentWord[$i];
      $nextWordCaractere = $nextWord[$i];
      $currentWordCaractereIndex = array_search($currentWordCaractere, $alphabet);
      $nextWordCaractereIndex = array_search($nextWordCaractere, $alphabet);

      if ($currentWordCaractere === $nextWordCaractere || $nextWordCaractereIndex === $currentWordCaractereIndex) continue;
      if ($currentWordCaractereIndex < $nextWordCaractereIndex) return -1;
      else return 1;
    }
    if ($currentWordLength < $nextWordLength) return -1;
    elseif ($currentWordLength > $nextWordLength) return 1;
    return 0;
  }

  function analyzeBooglansText($words) {
    $nonPrepositionWords = require_once('./fooWords.php');
    $alphabet = require('./alphabet.php');
    $caractereList = [];
    $wordsList = explode(" ", $words);
    $response = [
      "prepostions" => 0,
      "verbs" => [
        "count" => 0,
        "firstPersons" => 0
      ],
      "text" => "",
      "beautifullNumbers" => [
        "count" => 0,
        "numbers" => []
      ]
    ];

    foreach ($wordsList as $index => $word) {
      $response['prepostions'] = countBooglansPrepositions($word, $nonPrepositionWords, $response['prepostions']);
      $response['verbs'] = countBooglansVerbsAndPersons($word, $nonPrepositionWords, $response['verbs']);
      $response['beautifullNumbers'] = countBooglansBeautifullNumbers($word, $alphabet, $response['beautifullNumbers']);

      if(isset($caractereList[$word[0]]) && isset($caractereList[$word[0]][$word])) {
        if(!in_array($word, array_keys($caractereList[$word[0]]))) {
          $caractereList[$word[0]][$word] = $index;
        }
      } else {
        $caractereList[$word[0]][$word] = $index;
      }

    }

    foreach ($alphabet as $caractere) {
      uksort($caractereList[$caractere], 'compareByBooglansAlphabet');
      $response['text'] = $response['text']." ".implode(" ", array_keys($caractereList[$caractere]));
    }

    echo $response['prepostions']." preposições no texto \n";
    echo $response["verbs"]["count"]." verbos no texto, dos quais ".$response["verbs"]["firstPersons"]." estão em primeira pessoa \n";
    echo "O texto em ordem alfabética fica: \n";
    echo trim($response['text'])."\n";
    echo $response["beautifullNumbers"]["count"]." números bonitos distintos. \n";
  };

  analyzeBooglansText($text);

  ?>
