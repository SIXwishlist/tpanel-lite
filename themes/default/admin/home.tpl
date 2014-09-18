<script type="text/javascript" src="{{ @theme('js/jquery.js') }}"></script>
<script type="text/javascript" src="{{ @theme('js/dialog.js') }}"></script>
<script type="text/javascript">
jQuery(document).ready(function () {
	var UserList = {};
	UserList.page = 0;
	UserList.count = 0;
	UserList.perPage = 0;
	
	UserList.headerCell = function (data) {
		var cell = jQuery('<th></th>');
		cell.html(data);
		return cell;
	};
	
	UserList.cell = function (data) {
		var cell = jQuery('<td></td>');
		cell.html(data);
		return cell;
	};
	
	UserList.getUserLevel = function (lvl) {
		var levels = {0:'Activation Required', 1:'Client', 2:'Administrator'};
		return levels[lvl];
	};
	
	UserList.list = function () {
		jQuery.get('{{ @url('api/users/') }}'+UserList.page.toString(), function (result) {
			jQuery('.user-list-panel').children().remove();
			var tbl = jQuery('<table width="100%" class="user-list"></table>');
			
			var row = jQuery('<tr></tr>');
			row.append(UserList.headerCell('Username'));
			row.append(UserList.headerCell('Email Address'));
			row.append(UserList.headerCell('Web Space'));
			row.append(UserList.headerCell('User Level'));
			row.append(UserList.headerCell('Actions'));
			tbl.append(row);
			
			var users = result.users;
			for(var j=0;j<users.length;j++){
				var row = jQuery('<tr></tr>');
				var userLink = jQuery('<a href="#"></a>');
				userLink.html(users[j].username);
				userLink.click(function(userId) {
					return function () {
						UserList.showUser(userId);
						return false;
					};
				}(parseInt(users[j].user_id)));
				
				var deleteBtn = jQuery('<button>Remove</button>');
				deleteBtn.click(function(userId) {
					return function () {
						alert(userId);
						return false;
					};
				}(parseInt(users[j].user_id)));
				
				row.append(UserList.cell(userLink));
				row.append(UserList.cell(users[j].email));
				row.append(UserList.cell(users[j].webspace+' MB'));
				row.append(UserList.cell(UserList.getUserLevel(parseInt(users[j].user_level))));
				row.append(UserList.cell(deleteBtn));
				tbl.append(row);
			}
			UserList.page = result.page;
			UserList.count = result.count;
			UserList.perPage = result.perPage;
			
			jQuery('.user-list-panel').append(tbl);
			jQuery('.user-list-panel').append(UserList.getPaginator());
		});
	};
	
	UserList.showUser = function (userId) {
		jQuery.get('{{ @url('api/user/') }}'+userId, function (user) {
			Dialog.show('user-edit', function (dlg) {
				
			});
		});
	};
	
	UserList.getPaginator = function () {
		var pg = jQuery('<ul class="paginator"></ul>');
		for(var j=0;j<Math.ceil(UserList.count/UserList.perPage);j++){
			var link = jQuery('<li><a href="#">'+(j+1)+'</a></li>');
			link.find('a').click(function(page) {
				return function() {
					UserList.page = page;
					UserList.list();
					return false;
				};
			}(j));
			pg.append(link);
		}
		return pg;
	};
	
	UserList.list();
});
</script>
<div class="user-list-panel"></div>
<div class="overlay">
	<div class="dialog" id="user-edit">
		<div class="row">
			<label>Username</label>
			<div class="field" id="username"></div>
		</div>
		<div class="row">
			<label>Email</label>
			<div class="field">
				<input type="text" id="email" />
			</div>
		</div>
		<div class="row">
			<label>Full Name</label>
			<div class="field">
				<input type="text" id="full_name" />
			</div>
		</div>
		<div class="row">
			<label>Web Space</label>
			<div class="field">
				<input type="text" id="webspace" /> MB
			</div>
		</div>
		<div class="row">
			<label>User Level</label>
			<div class="field">
				<select id="user_level">
					<option value="0">Activation Required</option>
					<option value="1">Client</option>
					<option value="2">Administrator</option>
				</select>
			</div>
		</div>
		<div class="row">
			<button class="ok-btn">Save</button>
			<button class="cancel-btn">Cancel</button>
		</div>
	</div>
</div>
<div class="button-panel">
	<button id="new">Create New User</button>
</div>