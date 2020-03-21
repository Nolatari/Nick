<?php

$variables['page']['title'] = 'Dashboard';

$page = \Nick::Renderer()
  ->setType()
  ->setTemplate('dashboard')
  ->render();