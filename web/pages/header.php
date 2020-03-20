<?php

$header = \Nick::Renderer()
  ->setType()
  ->setTemplate('header')
  ->render($variables ?? NULL);