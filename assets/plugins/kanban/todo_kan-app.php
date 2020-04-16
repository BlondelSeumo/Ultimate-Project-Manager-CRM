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
                IDs.push($(this).attr("data-todo-id"));
            });
            var formData = {
                'todo_id': IDs,
                'status': newListID
            };
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/dashboard/change_todo_status/' + newListID, // the url where we want to POST
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

        function todo_kanban_init() {
            // Seeding
            <?php
            $todo_status = $this->admin_model->get_todo_status();
            foreach ($todo_status as $v_status) {
            $t_where = array('user_id' => $user_id, 'status' => $v_status['value']);
            if (!empty($where)) {
                $t_where = array_merge($t_where, $where);
            }
            $all_todo = $this->db->where($t_where)->order_by('order', 'ASC')->get('tbl_todo')->result();
            ?>
            addList('<?= $v_status['name']?>');
            <?php if (!empty($all_todo)) {
            foreach ($all_todo as $v_todo) {
            $text = null;
            $style = null;
            $due_date = $v_todo->due_date;
            $style = 'word-wrap: break-word;';
            if ($v_todo->status == 3) {
                $style .= 'text-decoration: line-through';
            }
            $text .= '<a data-todo-id="' . $v_todo->todo_id . '" style="' . $style . '" class="text-info" href="' . base_url() . 'admin/dashboard/new_todo' . $v_todo->todo_id . '">' . clear_textarea_breaks($v_todo->title) . '</a>';
            if ($v_todo->assigned != 0) {
                $a_userinfo = $this->db->where('user_id', $v_todo->assigned)->get('tbl_account_details')->row();
                $text .= '<small class="block">' . lang('assign_by') . ' ';
                $text .= '<a class="text-danger" href="' . base_url() . 'admin/user/user_details/' . $v_todo->assigned . '">' . $a_userinfo->fullname . '</a>';
                $text .= '</small>';
            }
            if ($v_todo->status != 3) {
                $text .= '<span class="block">' . daysleft($due_date) . '</span>';
            }
            ?>
            addTodo('<?= $text?>', <?= $v_status['id']?>, <?= $v_todo->order?>, false);
            <?php }
            }
            ?>
            updateCardCounts();
            moveCard(2, 1, 3);
            <?php }
            ?>

        }

        document.addEventListener("DOMContentLoaded", function () {
            todo_kanban_init();
        });

    })();
</script>
  
