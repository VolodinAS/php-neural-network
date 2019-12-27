<?php
/**
 * Created by PhpStorm.
 * User: VolodinAS
 * Date: 27.12.2019
 * Time: 1:49
 */

class Neuralink
{
    /**
     * Массив входящих данных
     * @var array $InputArray
     */
    private $InputArray = array();

    /**
     * Массив скрытых слоёв (указывается через запятую количество нейронов в каждом слое
     * @var array $HiddenLayersCount
     */
    private $HiddenLayersCount = array();

    /**
     * Данные, которые мы должны получить на выходе (образец)
     * @var array $TrainingArray
     */
    private $TrainingArray = array();

    /**
     * Количество выходных нейронов
     * @var int $OutputNeuronsCount
     */
    private $OutputNeuronsCount = 0;

    /**
     * Массив ИНИЦИАЛИЗИРОВАННЫХ выходных нейронов
     * @var array
     */
    private $OutputNeuronsArray = array();

    /**
     * Массив ИНИЦИАЛИЗИРОВАННЫХ нейронов скрытых слоёв
     * @var array
     */
    private $HiddenLayersArray = array();

    /**
     * Функция активации
     * @var string
     */
    private $ActivationFunc = "";

    /**
     * Минимальный генерируемый вес связи
     * @var int
     */
    private $WeightMinimum = 0.0;
    /**
     * Максимальный генерируемый вес связи
     * @var int
     */
    private $WeightMaximum = 0.0;
    /**
     * Количество знаков для округления
     * @var int
     */
    private $WeightRound = 0;

    /**
     * Коэффициент обучения
     * @var float
     */
    private $IQ = 0.0;

    /**
     * Массив с массивами весов
     * @var array
     */
    private $WeightsArray = array();









    /**
     * @param string $ActivationFunc
     */
    public function setActivationFunc($ActivationFunc = ActivationFunction::DEF)
    {
        $this->ActivationFunc = $ActivationFunc;
    }

    /**
     * @return float
     */
    public function getIQ()
    {
        return $this->IQ;
    }

    /**
     * @param float $IQ
     */
    public function setIQ($IQ)
    {
        $this->IQ = $IQ;
    }

    /**
     * @param int $WeightMinimum
     * @param int $WeightMaximum
     * @param int $WeightRound
     * @throws Exception количество знаков после запятой должно быть от 0 и больше
     */
    public function setWeightGeneration($WeightMinimum, $WeightMaximum, $WeightRound)
    {
        if (is_int($WeightRound) && ($WeightRound >= 0))
        {
            $this->WeightMinimum = $WeightMinimum;
            $this->WeightMaximum = $WeightMaximum;
            $this->WeightRound = $WeightRound;
        } else throw new Exception("Количество знаков после запятой должно быть 0 или больше");


    }

    /**
     * @return array
     */
    public function getInputArray()
    {
        return $this->InputArray;
    }

    /**
     * @param array $InputArray
     */
    public function setInputArray($InputArray)
    {
        $arr = array();
        for ($i=0; $i<count($InputArray); $i++)
        {
            $neuron = array();
            $neuron['v'] = $InputArray[$i];
            $neuron['e'] = 0;
            $arr[] = $neuron;
        }

        $neuronBias = array();
        $neuronBias['v'] = 1;
        $neuronBias['e'] = 0;
        $arr[] = $neuronBias;

        $this->InputArray = $arr;
        unset($arr);
    }

    /**
     * @return array
     */
    public function getTrainingArray()
    {
        return $this->TrainingArray;
    }

    /**
     * @param array $TrainingArray
     */
    public function setTrainingArray($TrainingArray)
    {
        $this->TrainingArray = $TrainingArray;
    }

    /**
     * @return int
     */
    public function getOutputNeuronsCount()
    {
        return $this->OutputNeuronsCount;
    }

    /**
     * @param $OutputNeuronsCount
     * @throws Exception если значение выходных нейронов не целое положительное число
     */
    public function setOutputNeuronsCount($OutputNeuronsCount)
    {
        if ( is_int($OutputNeuronsCount) && ($OutputNeuronsCount > 0) )
        {
            $this->OutputNeuronsCount = $OutputNeuronsCount;
            $this->initOutputNeurons();
        } else throw new Exception("Количество нейронов должно быть больше 0");
    }

    /**
     * @return array
     */
    public function getOutputNeuronsArray()
    {
        return $this->OutputNeuronsArray;
    }


    /**
     * @param array $HiddenLayersCount
     * @throws Exception проверяет, все ли нейроны слоёв являются целым положительным числом
     */
    public function setHiddenLayersCount()
    {
        $numargs = func_num_args();
        if ($numargs > 0)
        {
            $args = func_get_args();
            $arr = array();
            for ($i=0; $i<count($args); $i++)
            {
                $layerCount = $args[$i];
                if ( is_int($layerCount) && ($layerCount > 0) )
                {
                    $arr[] = $layerCount;
                } else throw new Exception("Количество нейронов должно быть больше 0");
            }
            $this->HiddenLayersCount = $arr;
            unset($arr);
            $this->initHiddenNeurons();
        } else throw new Exception("Должен быть хотя бы 1 скрытый слой");

    }


    /**
     * Генерация массива выходных нейронов
     * @return void
     */
    private function initOutputNeurons()
    {
        $arr = array();
        for ($i=0; $i<$this->OutputNeuronsCount; $i++)
        {
            $neuron = array();
            $neuron['v'] = 0;
            $neuron['e'] = 0;

            $arr[] = $neuron;
        }

        $this->OutputNeuronsArray = $arr;
        unset($arr);
    }

    /**
     * Инициализация скрытых нейронов
     * @return void
     */
    private function initHiddenNeurons()
    {
        for ($i=0; $i<count($this->HiddenLayersCount); $i++)
        {
            $arr = array();
            $hiddenLayerCount = $this->HiddenLayersCount[$i];
            for ($j=0; $j<$hiddenLayerCount; $j++)
            {
                $neuron = array();
                $neuron['v'] = 0;
                $neuron['e'] = 0;
                $arr[] = $neuron;
            }

            $neuronBias = array();
            $neuronBias['v'] = 1;
            $neuronBias['e'] = 0;
            $arr[] = $neuronBias;

            $this->HiddenLayersArray[] = $arr;
            unset($arr);
        }
    }

    /**
     * Генерация списка всех нейронов для последующей генерации весов
     * @return void
     */
    private function generateNeuronsList()
    {

    }

    public function generateWeights()
    {
        $arr = array();
        // принцип выстраивания связей:
        // входящие нейроны -> скрытые слои [Inp>Hid1]
        // скрытый слой -> скрытый слой [Hid1>Hid2, Hid2>Hid3, ..., HidN>HidN+1]
        // последний скрытый слой -> выходные нейроны [Hid>Out]

        $xx = count( $this->getInputArray() );
        $yy = count( $this->HiddenLayersArray[0] )-1;
        $Weights_Input2HiddenFirst = $this->generateArray( $xx, $yy );
        $arr[] = $Weights_Input2HiddenFirst;

        for ($i=0; $i<count($this->HiddenLayersArray); $i++)
        {
            if ( isset($this->HiddenLayersArray[$i+1]) )
            {
                $xx = count($this->HiddenLayersArray[$i]);
                $yy = count($this->HiddenLayersArray[$i+1])-1;
                $Weights_HiddenCurr2HiddenNext = $this->generateArray($xx, $yy);
                $arr[] = $Weights_HiddenCurr2HiddenNext;
            }
        }

        $xx = count( $this->HiddenLayersArray[ count($this->HiddenLayersArray)-1 ] );
        $yy = $this->getOutputNeuronsCount();
        $Weights_Input2HiddenLast = $this->generateArray( $xx, $yy );
        $arr[] = $Weights_Input2HiddenLast;

        $this->WeightsArray = $arr;
        unset($arr);
    }

    /**
     * @return array
     */
    public function getWeightsArray()
    {
        return $this->WeightsArray;
    }

    /**
     * @param $x int ширина массива
     * @param $y int высота массива
     * @return array
     */
    private function generateArray($x, $y)
    {
        $arr = array();
        for ($yy=0; $yy<$y; $yy++)
        {
            for ($xx=0; $xx<$x; $xx++)
            {
                $arr[$xx][$yy] = 0.0;
                $numberRound = "1";
                for ($i=0; $i<$this->WeightRound; $i++) $numberRound .= "0";
                $numberRound = intval($numberRound);
                $arr[$xx][$yy] = rand( $this->WeightMinimum * $numberRound, $this->WeightMaximum * $numberRound ) / $numberRound;
            }
        }
        return $arr;
    }

    /**
     * @param $x
     * @return float|int
     * @throws Exception
     */
    private function Sigmoid($x)
    {
        $val = 1 / ( 1 + pow( M_E , -1 * ($x) ) );
        if ($val !== FALSE ) return $val;
        else throw new Exception("Проблема при возведении константы Е в степень [-{$x}]");
    }

    public function train()
    {
        # распространяем сигнал от входных нейронов к I скрытому слою
        $this->HiddenLayersArray[0] = $this->forWards( $this->InputArray, $this->WeightsArray[0], $this->HiddenLayersArray[0] );

        # распространяем сигнал от первого скрытого до последнего скрытого слоя нейронов
        for ($i=0; $i<count($this->HiddenLayersArray); $i++)
        {
            if ( isset($this->HiddenLayersArray[$i+1]) )
            {
                $this->HiddenLayersArray[$i+1] = $this->forWards( $this->HiddenLayersArray[$i], $this->WeightsArray[$i+1], $this->HiddenLayersArray[$i+1] );
            }
        }

        # распространяем сигнал от последнего скрытого до выходных нейронов
        $this->OutputNeuronsArray = $this->forWards( $this->HiddenLayersArray[ count($this->HiddenLayersArray)-1 ], $this->WeightsArray[ count($this->WeightsArray)-1 ], $this->OutputNeuronsArray );

        # ищем ошибку выходных нейронов относительно тренировочных данных
        $this->OutputNeuronsArray = $this->fixOutError($this->TrainingArray, $this->OutputNeuronsArray);

        # ищем ошибку последнего скрытого слоя относительно выходных нейронов
        $this->HiddenLayersArray[ count($this->HiddenLayersArray)-1 ] = $this->findError( $this->HiddenLayersArray[ count($this->HiddenLayersArray)-1 ], $this->WeightsArray[ count($this->WeightsArray)-1 ], $this->OutputNeuronsArray );

        # ищем ошибки скрытых слоёв
        for ($i=count($this->HiddenLayersArray)-1; $i>=0; $i--)
        {
            if ( isset($this->HiddenLayersArray[$i-1]) )
            {
                $this->HiddenLayersArray[$i-1] = $this->findError($this->HiddenLayersArray[$i-1], $this->WeightsArray[$i], $this->HiddenLayersArray[$i]);
            }
        }

        # корректируем веса последнего скрытого слоя и выходного слоя
        $this->WeightsArray[ count($this->WeightsArray)-1 ] = $this->backWards( $this->HiddenLayersArray[ count($this->HiddenLayersArray)-1 ] , $this->WeightsArray[ count($this->WeightsArray)-1 ], $this->OutputNeuronsArray , $this->getIQ() );

        # корректируем веса скрытых слоёв
        for ($i=count($this->WeightsArray)-2; $i>=0; $i--)
        {
            if ( isset($this->WeightsArray[$i-1]) )
            {
                $this->WeightsArray[$i] = $this->backWards($this->HiddenLayersArray[$i-1], $this->WeightsArray[$i], $this->HiddenLayersArray[$i], $this->getIQ());
            }
        }

        # корректируем веса первого скрытого слоя и входных нейронов
        # корректируем веса первого скрытого слоя и входных нейронов
        $this->WeightsArray[0] = $this->backWards( $this->InputArray , $this->WeightsArray[ 0 ], $this->HiddenLayersArray[0] , $this->getIQ() );


//        debug($this->HiddenLayersArray);
    }

    /**
     * @param $inputNeurons
     * @param $weightsBetweenNeurons
     * @param $outputNeurons
     * @return array
     * @throws Exception обязательно указание функции активации
     */
    private function forWards($inputNeurons, $weightsBetweenNeurons, $outputNeurons)
    {
        $wt = $this->getSizeX($weightsBetweenNeurons);
        $ht = $this->getSizeY($weightsBetweenNeurons);

        $y = 0;
        while ($y < $ht)
        {
            $outputNeurons[$y]['v'] = 0;
            $x = 0;
            while ($x < $wt)
            {
                $outputNeurons[$y]['v'] = $outputNeurons[$y]['v'] + $inputNeurons[$x]['v'] * $weightsBetweenNeurons[$x][$y];
                $x++;
            }
            $outputNeurons[$y]['v'] = $this->activationValue($outputNeurons[$y]['v']);
            $y++;
        }
        return $outputNeurons;
    }

    /**
     * @param $trainData
     * @param $outputNeurons
     * @return array
     */
    private function fixOutError($trainData, $outputNeurons)
    {
        $wt = $this->getSizeX($trainData);
        $x = 0;
        while ($x < $wt)
        {
            $outputNeurons[$x]['e'] = $trainData[$x] - $outputNeurons[$x]['v'];
            $x++;
        }
        return $outputNeurons;
    }

    /**
     * @param $inputNeurons
     * @param $weightsBetweenNeurons
     * @param $outputNeurons
     * @return array
     */
    private function findError($inputNeurons, $weightsBetweenNeurons, $outputNeurons)
    {
        $wt = $this->getSizeX($weightsBetweenNeurons) - 1;
        $ht = $this->getSizeY($weightsBetweenNeurons);
//        debug($wt);
//        debug($ht);
        $x = 0;
        while ($x < $wt ) // по всем входным нейронам
        {
            $inputNeurons[$x]['e'] = 0;
            $y = 0;
            while ($y < $ht)  // по всем выходным нейронам
            {
                $inputNeurons[$x]['e'] = $inputNeurons[$x]['e'] + $weightsBetweenNeurons[$x][$y] * $outputNeurons[$y]['e'];
                $y++;
            }
            $x++;
        }
        return $inputNeurons;
    }

    /**
     * @param $inputNeurons
     * @param $weightsBetweenNeurons
     * @param $outputNeurons
     * @param $qoef float
     * @return array
     */
    private function backWards($inputNeurons, $weightsBetweenNeurons, $outputNeurons, $qoef)
    {
        $wt = $this->getSizeX($weightsBetweenNeurons);
        $ht = $this->getSizeY($weightsBetweenNeurons);

        $y = 0;
        while ($y < $ht)
        {
            $x = 0;
            while ($x < $wt)
            {
                $weightsBetweenNeurons[$x][$y] += $qoef * $outputNeurons[$y]['e']  * $inputNeurons[$x]['v'] * $outputNeurons[$y]['v'] * (1 - $outputNeurons[$y]['v']);
                $x++;
            }
            $y++;
        }
//        debug("back AFTER");
//        debug($weightsBetweenNeurons);
        return $weightsBetweenNeurons;
    }

    public function recognize()
    {
        # распространяем сигнал от входных нейронов к I скрытому слою
        $this->HiddenLayersArray[0] = $this->forWards( $this->InputArray, $this->WeightsArray[0], $this->HiddenLayersArray[0] );

        # распространяем сигнал от первого скрытого до последнего скрытого слоя нейронов
        for ($i=0; $i<count($this->HiddenLayersArray); $i++)
        {
            if ( isset($this->HiddenLayersArray[$i+1]) )
            {
                $this->HiddenLayersArray[$i+1] = $this->forWards( $this->HiddenLayersArray[$i], $this->WeightsArray[$i+1], $this->HiddenLayersArray[$i+1] );
            }
        }

        # распространяем сигнал от последнего скрытого до выходных нейронов
        $this->OutputNeuronsArray = $this->forWards( $this->HiddenLayersArray[ count($this->HiddenLayersArray)-1 ], $this->WeightsArray[ count($this->WeightsArray)-1 ], $this->OutputNeuronsArray );
    }

    /**
     * @param $arr
     * @return int
     */
    private function getSizeX($arr)
    {
        return count($arr);
    }

    /**
     * @param $arr
     * @return int
     */
    private function getSizeY($arr)
    {
        return count($arr[0]);
    }

    private function activationValue($val)
    {
        switch ($this->ActivationFunc)
        {
            case ActivationFunction::SIGMOID:
                return $this->Sigmoid($val);
            break;
            default:
                throw new Exception("Не выбрана функция активации [SIGMOID|DEF]");
            break;
        }
    }


    public function importWeights($jsonFile)
    {
        if ( file_exists($jsonFile) )
        {
            $this->WeightsArray = json_decode( file_get_contents($jsonFile) ,true );

//            debug($JSON);
        }
    }

    public function isTrained()
    {
        for ($i=0; $i<count($this->OutputNeuronsArray); $i++)
        {
            if ($this->OutputNeuronsArray[$i]['v'] > 0.8)
            {
                debug("Я думаю, что на картинке число $i");
            }
        }

        debug($this->OutputNeuronsArray);
    }

    public function squareError()
    {
        $Summary = 0.0;
        for ($i=0; $i<count($this->OutputNeuronsArray); $i++)
        {
//            debug($this->OutputNeuronsArray[$i]);
            $Summary += $this->OutputNeuronsArray[$i]['e'] * $this->OutputNeuronsArray[$i]['e'];
        }
        return $Summary;
    }

    public function goNext($sec)
    {
        echo '<meta http-equiv="refresh" content="'.$sec.'">';
    }
}

/**
 * Class ActivationFunction Константный класс с функциями активаций
 */
class ActivationFunction
{
    /**
     * Активация по типу сигмоиды
     */
    const SIGMOID = "SIGMOID";
    const DEF = "SIGMOID";
}