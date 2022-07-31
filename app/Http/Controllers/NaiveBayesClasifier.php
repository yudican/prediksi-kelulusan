<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Phpml\Classification\NaiveBayes;

class NaiveBayesClasifier extends NaiveBayes
{
    /**
     * Calculates the probability P(label|sample_n)
     *
     * @param array $sample
     * @param int $feature
     * @param string $label
     * @return float
     */
    private function sampleProbability($sample, $feature, $label)
    {
        $value = $sample[$feature];
        if ($this->dataType[$label][$feature] == self::NOMINAL) {
            if (
                !isset($this->discreteProb[$label][$feature][$value]) ||
                $this->discreteProb[$label][$feature][$value] == 0
            ) {
                return self::EPSILON;
            }
            return $this->discreteProb[$label][$feature][$value];
        }
        $std = $this->std[$label][$feature];
        $mean = $this->mean[$label][$feature];
        // Calculate the probability density by use of normal/Gaussian distribution
        // Ref: https://en.wikipedia.org/wiki/Normal_distribution
        //
        // In order to avoid numerical errors because of small or zero values,
        // some libraries adopt taking log of calculations such as
        // scikit-learn did.
        // (See : https://github.com/scikit-learn/scikit-learn/blob/master/sklearn/naive_bayes.py)
        $pdf  =  -0.5 * log(2.0 * pi() * $std * $std);
        // $value = is_string($value) ? 0 : $value;
        $pdf -= 0.5 * pow($value - $mean, 2) / ($std * $std);
        return $pdf;
    }
}
