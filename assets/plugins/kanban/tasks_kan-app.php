<script type="text/javascript">
    /**
     * Tiny little kanban board in vanilla JS
     * Might not work effectively on older browsers
     * Uses HTML5 Drag API
     * `webkitMatchesSelector` might not support older engines
     *
     * @author: Elton Jain
     */

    (function () {
        // Cache common DOM
        var UI = {
                elBoard: document.getElementById('board'),
                elCardPlaceholder: null,
            },
            lists = [],
            todos = [],
            isDragging = false,
            _listCounter = 0, // To hold last ID/index to avoid .length based index
            _cardCounter = 0; // To hold last ID/index to avoid .length based index

        // Live binding event listener (like jQuery's .on)
        function live(eventType, selector, callback) {
            document.addEventListener(eventType, function (e) {
                if (e.target.webkitMatchesSelector(selector)) {
                    callback.call(e.target, e);
                }
            }, false);
        }

        // Draggable Cards
        live('dragstart', '.list .card', function (e) {
            isDragging = true;
            e.dataTransfer.setData('text/plain', e.target.dataset.id);
            e.dataTransfer.dropEffect = "copy";
            e.target.classList.add('dragging');
        });
        live('dragend', '.list .card', function (e) {

            this.classList.remove('dragging');
            UI.elCardPlaceholder && UI.elCardPlaceholder.remove();
            UI.elCardPlaceholder = null;
            isDragging = false;


        });

        // Dropzone
        live('dragover', '.list, .list .card, .list .card-placeholder', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = "move";
            if (this.className === "list") { // List
                this.appendChild(getCardPlaceholder());
            } else if (this.className.indexOf('card') !== -1) { // Card
                this.parentNode.insertBefore(getCardPlaceholder(), this);
            }

        });

        live('drop', '.list, .list .card-placeholder', function (e) {
            e.preventDefault();
            if (!isDragging) return false;
            var todo_id = +e.dataTransfer.getData('text');
            var todo = getTodo({_id: todo_id});

            var newListID = null;
            if (this.className === 'list') { // Dropped on List
                newListID = this.dataset.id;
                this.appendChild(todo.dom);
            } else { // Dropped on Card Placeholder
                newListID = this.parentNode.dataset.id;
                this.parentNode.replaceChild(todo.dom, this);
            }
            IDs = [];
            $('#list_' + newListID).find("a").each(function () {
                IDs.push($(this).attr("data-task-id"));
            });
            var formData = {
                'task_id': IDs,
                'task_status': newListID
            };
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/tasks/change_tasks_status/' + newListID, // the url where we want to POST
                data: formData, // our data object
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                success: function (res) {
                    console.log(res);
                    if (res) {
                        toastr[res.status](res.message);
                    } else {
                        alert('There was a problem with AJAX');
                    }
                }
            })
            moveCard(todo_id, +newListID);
        });

        function createCard(text, listID, index) {

            if (!text || text === '') return false;
            var newCardId = ++_cardCounter;
            var card = document.createElement("div");
            var list = getList({_id: listID});
            card.draggable = true;
            card.dataset.id = newCardId;
            card.dataset.listId = listID;
            card.id = 'todo_' + newCardId;
            card.className = 'card';
            card.innerHTML = text.trim();
            var todo = {
                _id: newCardId,
                listID: listID,
                text: text,
                dom: card,
                index: index || list.cards + 1 // Relative to list
            };
            todos.push(todo);

            // Update card count in list
            ++list.cards;
            return card;
        }


        function addTodo(text, listID, index, updateCounters) {
            listID = listID || 1;
            if (!text) return false;
            var list = document.getElementById('list_' + listID);
            var card = createCard(text, listID, index);
            if (index) {
                list.insertBefore(card, list.children[index]);
            } else {
                list.appendChild(card);
            }

            // Don't update DOM if said so
            if (updateCounters !== false) updateCardCounts();
        }

        function addList(name) {
            name = name.trim();
            if (!name || name === '') return false;
            var newListID = ++_listCounter;
            var list = document.createElement("div");
            var heading = document.createElement("h3");
            var listCounter = document.createElement("span");


            list.dataset.id = newListID;
            list.id = 'list_' + newListID;
            list.className = "list";
            list.appendChild(heading);


            heading.className = "listname";
            heading.innerHTML = name;
            heading.appendChild(listCounter)

            listCounter.innerHTML = 0;
            lists.push({
                _id: newListID,
                name: name,
                cards: 0,
                elCounter: listCounter
            });
            UI.elBoard.append(list);
        }

        function getList(obj) {
            return _.find(lists, obj);
        }

        function getTodo(obj) {
            return _.find(todos, obj);
        }

        // Update Card Counts
        // Updating DOM objects that are cached for performance
        function updateCardCounts(listArray) {

            lists.map(function (list) {
                list.elCounter.innerHTML = list.cards;
            })
        }

        function moveCard(cardId, newListId, index) {
            if (!cardId) return false;
            try {
                var card = getTodo({_id: cardId});
                if (card.listID !== newListId) { // If different list
                    --getList({_id: card.listID}).cards;
                    card.listID = newListId;
                    ++getList({_id: newListId}).cards;
                    updateCardCounts();
                }

                if (index) {
                    card.index = index;
                }


            } catch (e) {
                console.log(e.message)
            }
        }

        function getCardPlaceholder() {
            if (!UI.elCardPlaceholder) { // Create if not exists
                UI.elCardPlaceholder = document.createElement('div');
                UI.elCardPlaceholder.className = "card-placeholder";
            }
            return UI.elCardPlaceholder;
        }

        function task_kanban_init() {
            // Seeding
            <?php
            $tasks_status = $this->tasks_model->get_statuses();
            foreach ($tasks_status as $v_status) {
            $all_tasks = $this->tasks_model->get_permission('tbl_task', array('task_status' => $v_status['value']));
            ?>
            addList('<?= $v_status['name']?>');
            <?php if (!empty($all_tasks)) {
            foreach ($all_tasks as $v_task) {
            $total_comments = count($this->db->where('task_id', $v_task->task_id)->get('tbl_task_comment')->result());
            $total_attachment = count($this->db->where('task_id', $v_task->task_id)->get('tbl_task_attachment')->result());
            $text = null;
            $due_date = $v_task->due_date;
            $due_time = strtotime($due_date);
            $current_time = strtotime(date('Y-m-d'));
            $text .= '<a data-task-id="' . $v_task->task_id . '" style="word-wrap: break-word;" class="text-info" href="' . base_url() . 'admin/tasks/view_task_details/' . $v_task->task_id . '">' . clear_textarea_breaks($v_task->task_name) . '</a>';
            if ($current_time > $due_time && $v_task->task_progress < 100) {
                $text .= '<span class="label label-danger pull-right">' . lang('overdue') . '</span>';
            }
            $text .= '<div class=" mb-sm progress progress-xs progress-striped active"><div class="progress-bar progress-bar-' . (($v_task->task_progress >= 100) ? "success" : "primary") . 'data-toggle="tooltip" data-original-title="' . $v_task->task_progress . '%" style="width:' . $v_task->task_progress . '%"></div></div>';

            if ($v_task->permission != 'all') {
                $get_permission = json_decode($v_task->permission);
                if (!empty($get_permission)) {
                    foreach ($get_permission as $permission => $v_permission) {
                        $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                        if (!empty($user_info)) {
                            if ($user_info->role_id == 1) {
                                $label = 'circle-danger';
                            } else {
                                $label = 'circle-success';
                            }
                            $text .= '<img style="width:20px;height:20px"  src="' . base_url(staffImage($permission)) . '"class="img-circle" alt=""><span class="circle' . $label . ' circle-lg"></span></a>';
                        }
                    }
                }

            } else {
                $text .= '<strong class="text-sm">' . lang('everyone') . '</strong>';
            }
            $text .= '<div class="pull-right mt-sm">';
            $text .= ' <span class="" data-placement="top" data-toggle="tooltip" title="' . lang('comments') . '"><i class="fa fa-comments"></i> ' . $total_comments . ' </span>' . ' ';
            $text .= ' <span class="" data-placement="top" data-toggle="tooltip"  title="' . lang('attachment') . '"><i class="fa fa-paperclip"></i> ' . $total_attachment . ' </span>';
            $text .= '</div>';
            ?>
            addTodo('<?= $text?>', <?= $v_status['id']?>, <?= $v_task->index_no?>, false);
            <?php }
            }
            ?>
            updateCardCounts();
            moveCard(2, 1, 3);
            <?php }
            ?>

        }

        document.addEventListener("DOMContentLoaded", function () {
            task_kanban_init();
        });

    })();
</script>
  
