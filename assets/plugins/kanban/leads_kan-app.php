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
            var ID = '#list_' + newListID;
            var lead_status_id = $(ID).attr("data-status-id");
            IDs = [];
            $(ID).find("a").each(function () {
                IDs.push($(this).attr("data-leads-id"));
            });
            var $span = $('<span>' + IDs.length + '</span>');
            $(ID + '> h3.listname span').replaceWith($span);

            var formData = {
                'leads_id': IDs,
            };
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/leads/change_leads_status/' + lead_status_id, // the url where we want to POST
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

        function addList(name, id) {
            name = name.trim();
            if (!name || name === '') return false;
            var newListID = ++_listCounter;
            var list = document.createElement("div");
            var heading = document.createElement("h3");
            var listCounter = 0;

            list.dataset.id = newListID;
            list.dataset.statusId = id;
            list.id = 'list_' + newListID;
            list.className = "list";
            list.appendChild(heading);


            heading.className = "listname";
            heading.innerHTML = name;

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

        function leads_kanban_init() {
            <?php
            $leads_status = $this->db->order_by('order_no', 'ASC')->get('tbl_lead_status')->result();
            foreach ($leads_status as $key => $v_leads_status) {
            $k_all_leads = $this->items_model->get_permission('tbl_leads', array('lead_status_id' => $v_leads_status->lead_status_id));
            if (!empty($v_leads_status->lead_type)) {
                $lead_type = '(' . lang($v_leads_status->lead_type) . ')';
            } else {
                $lead_type = null;
            }
            ?>
            addList('<?= clear_textarea_breaks($v_leads_status->lead_status) . ' ' . clear_textarea_breaks($lead_type) ?><span><?= count($k_all_leads)?></span>',<?= $v_leads_status->lead_status_id?>);
            <?php if (!empty($k_all_leads)) {
            foreach ($k_all_leads as $v_k_leads) {
            if ($v_k_leads->converted_client_id == 0) {
            $k_lead_source = $this->db->where('lead_source_id', $v_k_leads->lead_source_id)->get('tbl_lead_source')->row();
            $total_calls = count($this->db->where('leads_id', $v_k_leads->leads_id)->get('tbl_calls')->result());
            $total_meetings = count($this->db->where('leads_id', $v_k_leads->leads_id)->get('tbl_mettings')->result());
            $total_comments = count($this->db->where('leads_id', $v_k_leads->leads_id)->get('tbl_task_comment')->result());
            $total_tasks = count($this->db->where('leads_id', $v_k_leads->leads_id)->order_by('leads_id', 'DESC')->get('tbl_task')->result());
            $total_attachment = count($this->db->where('leads_id', $v_k_leads->leads_id)->get('tbl_task_attachment')->result());
            $text = null;
            $text .= '<a data-lead-status-id="' . $v_leads_status->lead_status_id . '" data-leads-id="' . $v_k_leads->leads_id . '" style="word-wrap: break-word;" class="text-info" href="' . base_url() . 'admin/leads/leads_details/' . $v_k_leads->leads_id . '">' . clear_textarea_breaks($v_k_leads->lead_name) . '</a>';
            $text .= '<div class="">';
            $text .= '<strong class="text-sm  m0 p0 ">' . lang('source') . ': <span class="text-danger">' . clear_textarea_breaks($k_lead_source->lead_source) . '</span></strong>';
            $text .= '<div class="pull-right text-sm mt-sm">';
            $text .= ' <span class="" data-placement="top" data-toggle="tooltip" title="' . lang('comments') . '"><i class="fa fa-phone"></i> ' . $total_calls . ' </span>' . ' ';
            $text .= ' <span class="" data-placement="top" data-toggle="tooltip" title="' . lang('mettings') . '"><i class="fa fa-delicious"></i> ' . $total_meetings . ' </span>' . ' ';
            $text .= ' <span class="" data-placement="top" data-toggle="tooltip" title="' . lang('comments') . '"><i class="fa fa-comments"></i> ' . $total_comments . ' </span>' . ' ';
            $text .= ' <span class="" data-placement="top" data-toggle="tooltip"  title="' . lang('attachment') . '"><i class="fa fa-paperclip"></i> ' . $total_attachment . ' </span>';
            $text .= ' <span class="" data-placement="top" data-toggle="tooltip"  title="' . lang('attachment') . '"><i class="fa fa-tasks"></i> ' . $total_tasks . ' </span>';
            $text .= '</div>';
            $text .= '</div>';
            ?>
            addTodo('<?= $text?>', <?= $key + 1?>, <?= $v_k_leads->index_no?>, false);
            <?php }}} ?>
            updateCardCounts();
            moveCard(2, 1, 3);
            <?php }
            ?>

        }

        document.addEventListener("DOMContentLoaded", function () {
            leads_kanban_init();
        });

    })();
</script>
  
