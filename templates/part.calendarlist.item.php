<?php
/**
 * ownCloud - Calendar App
 *
 * @author Raghu Nayyar
 * @author Georg Ehrke
 * @copyright 2016 Raghu Nayyar <beingminimal@gmail.com>
 * @copyright 2016 Georg Ehrke <oc.list@georgehrke.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
?>
<span class="calendarCheckbox"
	  ng-click="triggerEnable(item)"
	  ng-show="item.displayActions()"
	  ng-style="{ background : item.getCalendar().getEnabled() == true ? item.getCalendar().getColor() : 'transparent' }">
</span>
<span class="icon-loading-small pull-left"
	  ng-show="item.displaySpinner()">
</span>
<a class="action permanent"
   href="#"
   ng-click="triggerEnable(item)"
   ng-show="!item.isEditing()"
   title="{{ item.getCalendar().getDisplayname() }}">
	<span class="icon icon-error"
		  ng-if="item.getCalendar().hasWarnings()"
		  title="<?php p($l->t('Some events in this calendar are broken. Please check the JS console for more info.')); ?>">
		&nbsp;&nbsp;&nbsp;&nbsp;
	</span>
	{{ item.getCalendar().getDisplayname() }}
</a>
<span class="utils"
	  ng-show="item.displayActions()">
	<span class="action">
		<span class="calendarlist-icon share icon-share permanent"
			  ng-click="item.toggleEditingShares()"
			  ng-if="item.getCalendar().isShareable()"
			  title="<?php p($l->t('Share Calendar')) ?>">
		</span>
	</span>
	<span class="action">
		<span class="icon-more"
			  href="#"
			  on-toggle-show="#more-actions-{{ $id }}"
			  title="<?php p($l->t('More')); ?>">
		</span>
	</span>
</span>

<div id="more-actions-{{ $id }}"
	 class="app-navigation-entry-menu hidden">
	<ul>
		<li ng-show="item.getCalendar().arePropertiesWritable()">
			<button class="icon-rename svg"
					ng-click="prepareUpdate(item)"
					title="<?php p($l->t('Edit')); ?>">
			</button>
		</li>
		<li>
			<button class="icon-public svg"
					ng-click="item.showCalDAVUrl()"
					title="<?php p($l->t('CalDAV')); ?>">
			</button>
		</li>
		<li>
			<button class="icon-download svg"
					ng-click="download(item)"
					title="<?php p($l->t('Export')); ?>">
			</button>
		</li>
		<li>
			<button class="icon-delete svg"
					ng-click="remove(item)"
					title="<?php p($l->t('Delete')); ?>">
			</button>
		</li>
	</ul>
</div>

<fieldset class="editfieldset"
		  ng-show="item.isEditing()">
	<form ng-submit="performUpdate(item)">
		<input class="app-navigation-input"
			   ng-model="item.displayname"
			   type="text"/>
		<colorpicker class="colorpicker"
					 selected="item.color">
		</colorpicker>
		<div class="buttongroups">
			<button class="primary icon-checkmark-white accept-button">
			</button>
			<button class="btn close-button icon-close"
					ng-click="item.cancelEditor()">
			</button>
		</div>
	</form>
</fieldset>
<fieldset class="editfieldset"
		  ng-show="item.displayCalDAVUrl()">
	<input class="input-with-button-on-right-side"
		   ng-value="item.getCalendar().getCalDAV()"
		   readonly
		   type="text"/>
	<button class="btn icon-close button-next-to-input"
			ng-click="item.hideCalDAVUrl()">
	</button>
</fieldset>
<div class="calendarShares"
	 ng-show="item.isEditingShares()">
	<i class="glyphicon glyphicon-refresh"
	   ng-show="loadingSharees">
	</i>
	<input class="shareeInput"
		   ng-model="item.selectedSharee"
		   placeholder="<?php p($l->t('Share with users or groups')); ?>"
		   type="text"
		   typeahead-on-select="onSelectSharee($item, $model, $label, item.getCalendar())"
		   typeahead-loading="loadingSharees"
		   uib-typeahead="sharee.display for sharee in findSharee($viewValue, item.getCalendar())">
	<ul class="calendar-share-list">
		<li class="calendar-share-item"
			ng-repeat="userShare in item.getCalendar().getUserShares()">
			{{ userShare.displayname }} -
			<input id="checkbox_sharedWithUser_{{ $parent.$index }}_{{ $id }}"
				   name="editable"
				   ng-change="updateExistingUserShare(item.getCalendar(), userShare.id, userShare.writable)"
				   ng-model="userShare.writable"
				   type="checkbox"
				   value="edit">
			<label for="checkbox_sharedWithUser_{{ $parent.$index }}_{{ $id }}">
				<?php p($l->t('can edit')); ?>
			</label>
			<span class="utils hide">
				<span class="action">
					<span class="icon-delete"
						  href="#"
						  id="calendarlist-icon delete"
						  ng-click="unshareFromUser(item.getCalendar(), userShare.id)"
						  title="<?php p($l->t('Delete')); ?>">
					</span>
				</span>
			</span>
		</li>
		<li class="calendar-share-item"
			ng-repeat="groupShare in item.getCalendar().getGroupShares()">
			{{ groupShare.displayname }} (<?php p($l->t('group')); ?>) -
			<input id="checkbox_sharedWithGroup_{{ $parent.$index }}_{{ $id }}"
				   name="editable"
				   ng-change="updateExistingGroupShare(item.getCalendar(), groupShare.id, groupShare.writable)"
				   ng-model="groupShare.writable"
				   type="checkbox"
				   value="edit">
			<label for="checkbox_sharedWithGroup_{{ $parent.$index }}_{{ $id }}">
				<?php p($l->t('can edit')); ?>
			</label>
			<span class="utils hide">
				<span class="action">
					<span class="icon-delete"
						  href="#"
						  id="calendarlist-icon delete"
						  ng-click="unshareFromGroup(item.getCalendar(), groupShare.id)"
						  title="<?php p($l->t('Delete')); ?>">
					</span>
				</span>
			</span>
		</li>
	</ul>
</div>
