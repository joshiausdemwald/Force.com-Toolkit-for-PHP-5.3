<?php
namespace Codemitte\ForceToolkit\Soql\Tokenizer;

class TokenizerException extends \Exception
{
    private $soqlLineNo;

    private $soqlLinePos;

    private $input;

    /**
     * @param string $message
     * @param int $soqlLineNo
     * @param \Exception $soqlLinePos
     * @param null $input
     * @internal param int $line
     * @internal param \Exception $pos
     */
    public function __construct($message, $soqlLineNo, $soqlLinePos, $input)
    {
        $this->input = $input;

        $this->soqlLineNo = $soqlLineNo;

        $this->soqlLinePos  = $soqlLinePos;

        parent::__construct(rtrim($message, ' .;:!') . ' near line ' . $soqlLineNo. ', position ' . $soqlLinePos . ": \n\n" . $this->getMarkedInput());
    }

    /**
     * @return int
     */
    public function getSoqlLineNo()
    {
        return $this->soqlLineNo;
    }

    /**
     * @return int
     */
    public function getSoqlLinePos()
    {
        return $this->soqlLinePos;
    }

    /**
     * @return string|null
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * SELECT FROM * BLAH BLUB
     * -------^
     */
    public function getMarkedInput()
    {
        // create line matrix
        $matrix = preg_split ('/$\R?^/m', $this->input);

        $part = '';

        if(isset($matrix[$this->soqlLineNo]))
        {
            $part = $matrix[$this->soqlLineNo];

            $part .= "\n" . str_repeat('-', $this->getSoqlLinePos()) . "^";
        }
        return $part;
    }
}
