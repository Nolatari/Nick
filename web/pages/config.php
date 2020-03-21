<?php

if (isset($_GET['export'])) {
  \Nick::Config()->export();
} elseif (isset($_GET['import'])) {
  \Nick::Config()->import();
} elseif (isset($_GET['difference'])) {
  // @TODO
}

// @TODO