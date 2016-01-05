<?php
include_once('FancyConnector.php');
class DashboardHandler extends FancyConnector {
	protected function init() {
		if ($this->fancyVars['apiVersion'] >= 2000) {
			//This is running on the old system
			$this->prepareStatements();
		}
		else {
			$this->oldPrepareStatements();
		}
	}

	public function deleteSite($name) {
		$this->con->query("DROP TABLE IF EXISTS `{$this->con->real_escape_string($name)}`;");
		return;
	}

	public function prepareStatements() {
		//Prepare Statements using the new system
		$this->preparedStatements['getElementByID'] = $this->con->prepare("/*".MYSQLND_QC_ENABLE_SWITCH."*/ SELECT `name`, `html` FROM `elements` WHERE `site` = ? AND `id` = ?;");
		$this->preparedStatements['getElements'] = $this->con->prepare("/*".MYSQLND_QC_ENABLE_SWITCH."*/ SELECT `id`, `name` FROM `elements` WHERE `site` = ?;");
		$this->preparedStatements['deleteElement'] = $this->con->prepare("DELETE FROM `elements` WHERE `name` = ? AND `site` = ?;");
		$this->preparedStatements['newElement'] = $this->con->prepare("INSERT INTO `elements` (`name`, `html`, `site`) VALUES (?, ?, ?);");
		$this->preparedStatements['updateElement'] = $this->con->prepare("UPDATE `elements` SET `name`=?, `html`=? WHERE `id` = ?;");
		$this->preparedStatements['getSites'] = $this->con->prepare("/*".MYSQLND_QC_ENABLE_SWITCH."*/ SELECT `name` FROM `sites` WHERE 1;");
		return;
	}

	public function oldPrepareStatements() {
		if ($this->site) {
			$this->preparedStatements['getElementByID'] = $this->con->prepare("/*".MYSQLND_QC_ENABLE_SWITCH."*/ SELECT `name`, `html` FROM `{$this->con->real_escape_string($this->site)}` WHERE `id` = ?;");
			$this->preparedStatements['getElements'] = $this->con->prepare("/*".MYSQLND_QC_ENABLE_SWITCH."*/ SELECT `id`, `name` FROM `{$this->con->real_escape_string($this->site)}` WHERE 1;");
			$this->preparedStatements['deleteElement'] = $this->con->prepare("DELETE FROM `{$this->con->real_escape_string($this->site)}` WHERE `name` = ?;");
			$this->preparedStatements['newElement'] = $this->con->prepare("INSERT INTO `{$this->con->real_escape_string($this->site)}` (`name`, `html`) VALUES (?, ?);");
			$this->preparedStatements['updateElement'] = $this->con->prepare("UPDATE `{$this->con->real_escape_string($this->site)}` SET `name`=?, `html`=? WHERE `id` = ?;");
		}
		return;
	}

	public function getElement($id) {
		if ($this->fancyVars['apiVersion'] >= 2000) {
			$this->preparedStatements['getElementByID']->bind_param('ss', $this->site, $id);
		}
		else {
			$this->preparedStatements['getElementByID']->bind_param('s', $id);
		}
		$this->preparedStatements['getElementByID']->execute();
		$this->preparedStatements['getElementByID']->bind_result($name, $element);
		while ($this->preparedStatements['getElementByID']->fetch()) { return json_encode(array('name' => $name, 'html' => $element)); }
	}

	public function getElements() {
		if ($this->fancyVars['apiVersion'] >= 2000) {
			$this->preparedStatements['getElements']->bind_param('s', $this->site);
		}
		$this->preparedStatements['getElements']->execute();
		$this->preparedStatements['getElements']->bind_result($id, $name);
		$elementList = array();
		while ($this->preparedStatements['getElements']->fetch()) { $elementList[] = array('id' => $id, 'name' => $name); }
		return json_encode($elementList);
	}

	public function deleteElement($name) {
		if ($this->fancyVars['apiVersion'] >= 2000) {
			$this->preparedStatements['deleteElement']->bind_param('ss', $name, $this->site);
		}
		else {
			$this->preparedStatements['deleteElement']->bind_param('s', $name);
		}
		$this->preparedStatements['deleteElement']->execute();
		return;
	}

	public function newElement($name, $html) {
		if ($this->fancyVars['apiVersion'] >= 2000) {
			$this->preparedStatements['newElement']->bind_param('sss', $name, $html, $this->site);
		}
		else {
			$this->preparedStatements['newElement']->bind_param('ss', $name, $html);
		}
		$this->preparedStatements['newElement']->execute();
		return;
	}

	public function updateElement($name, $html, $id) {
		$this->preparedStatements['updateElement']->bind_param('ssi', $name, $html, $id);
		$this->preparedStatements['updateElement']->execute();
		return;
	}

	public function getSites() {
		if ($this->fancyVars['apiVersion'] >= 2000) {
			$this->preparedStatements['getSites']->execute();
			$this->preparedStatements['getSites']->bind_result($name);
			$sites = array();
			while ($this->preparedStatements['getSites']->fetch()) { $sites[] = $name; }
			return json_encode($sites);
		}
		else {
			$sql = $this->con->query("show tables;");
			$res = array();
			$sites = array();
			foreach ($sql as $row) {
				$res[] = $row;
			}
			foreach ($res as $x) {
				$sites[] = $x['Tables_in_'.$this->fancyVars['dbname']];
			}
			foreach ($sites as $key => $value) {
				$sites[$key] = stripslashes($value);
			}
			return json_encode($sites);
		}
	}

	public function newSite($name) {
		if ($this->fancyVars['apiVersion'] >= 2000) {
			//TODO: New database layout
			return;
		}
		else {
			$this->con->query("SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";");
			$this->con->query("CREATE TABLE `{$this->con->real_escape_string($name)}` (`id` int(11) NOT NULL, `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, `html` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
			$this->con->query("ALTER TABLE `{$this->con->real_escape_string($name)}` ADD PRIMARY KEY (`id`);");
			$this->con->query("ALTER TABLE `{$this->con->real_escape_string($name)}` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
		}
		return;
	}
}