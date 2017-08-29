edit = 0;

function appendTodo(id, title, is_completed) {
    var li = document.createElement('li');
    var d = document.createElement('div');
    $(d).addClass("view");
    var i = document.createElement('input');
    $(i).addClass("toggle").attr("type", "checkbox").val(id);
    if (is_completed == 1) {
        $(li).addClass("completed");
        $(i).prop('checked', true);
    }
    $(d).append(i);
    var l = document.createElement('label');
    $(l).text(title);
    $(d).append(l);
    var b = document.createElement('button');
    $(b).addClass("destroy");
    $(d).append(b);
    $(li).append(d);
    var e = document.createElement('input');
    $(e).addClass("edit").val(title);
    $(li).append(e);
    $(".todo-list").append(li);
}

function getTodos() {
    $(".todo-list").html("");

    fetch("/todos.php", {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        method: "POST",
        body: JSON.stringify({hash: localStorage.getItem('hash')})
    }).then(function (response) {
        if (response.ok) {
            response.json().then(function (data) {
                $.each(data.todos, function (key, val) {
                    appendTodo(val.id, val.title, val.is_completed);
                });
            });
        }
    });
}

function countTodo() {
    var c = $(".todo-list li").not(".completed").length;
    $(".todo-count strong").text(c);
    if (c > 0) $(".toggle-all").prop('checked', false);
    else $(".toggle-all").prop('checked', true);
    if ($(".todo-list li.completed").length > 0) $(".clear-completed").show();
    else $(".clear-completed").hide();
    if ($(".todo-list li").length > 0) {
        $(".footer").show();
        $(".toggle-all").show();
    }
    else {
        $(".footer").hide();
        $(".toggle-all").hide();
    }
    checkFilter();
}

function checkFilter() {
    var h = location.hash;
    if (localStorage.getItem('hash')) {
        if (h == "#/signout") {
            $("#hello_user").text();
            location.hash = '#/signin';
            localStorage.clear();
            $(".signup input[name='email']").val("");
            $(".signup input[name='password']").val("");
            $(".signup input[name='password2']").val("");
        }

        if (h == "#/") {
            $('.form.signin').hide();
            $('.form.signup').hide();
            $(".signin").hide();
            $(".signup").hide();
            $(".signout").show();
            $(".guest").hide();
            $(".user").show();
            $(".todoapp").show();
            $(".todo-list li").show();
            $('.filters a.selected').removeClass("selected");
            $('.filters a[href="' + h + '"]').addClass("selected");
        }

        if (h == "#/active") {
            $(".todo-list li").hide();
            $(".todo-list li").not(".completed").show();
            $('.form.signin').hide();
            $('.form.signup').hide();
            $(".signin").hide();
            $(".signup").hide();
            $(".signout").show();
            $(".guest").hide();
            $(".user").show();
            $(".todoapp").show();
            $('.filters a.selected').removeClass("selected");
            $('.filters a[href="' + h + '"]').addClass("selected");

            console.log('active');
        }
        if (h == "#/completed") {
            $('.todoapp').show();
            $(".todo-list li").hide();
            $(".todo-list li.completed").show();
            $('.form.signin').hide();
            $('.form.signup').hide();
            $(".signin").hide();
            $(".signup").hide();
            $(".signout").show();
            $(".guest").hide();
            $(".user").show();
            $(".todoapp").show();
            $('.filters a.selected').removeClass("selected");
            $('.filters a[href="' + h + '"]').addClass("selected");
        }
    } else {
        if (h == "#/signup") {
            $(".signin").hide();
            $(".signup").show();
        }

        if (h == "#/signin") {
            $(".signup").hide();
            $(".signin").show();
        }

        $(".guest").show();
        $(".user").hide();
        $(".todoapp").hide();
    }
}

function updateTodo(id, title) {
    $.post("/update_title_todo.php", {hash: localStorage.getItem('hash'), id: id, title: title}, function () {
        edit = 0;
        $(".todo-list li.editing label").text(title);
        $(".todo-list li.editing").removeClass("editing");
    });
}

$(function () {
    $("#dosignup").click(function () {
        var email = $.trim($(".signup input[name='email']").val());
        var password = $.trim($(".signup input[name='password']").val());
        var password2 = $.trim($(".signup input[name='password2']").val());

        fetch("/signup.php", {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            method: "POST",
            body: JSON.stringify({email: email, password: password, password2: password2})
        }).then(function (response) {
            if (response.ok) {
                response.json().then(function (data) {
                    localStorage.setItem('hash', data.hash);
                    getTodos();
                    $("#hello_user").text(email);
                    location.hash = "#/active";

                });
            } else {
                response.json().then(function (data) {
                    alert(data.desc);
                });
            }
        });
    });

    $("#dosignin").click(function () {
        var email = $.trim($(".signin input[name='email']").val());
        var password = $.trim($(".signin input[name='password']").val());

        fetch("/signin.php", {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            method: "POST",
            body: JSON.stringify({email: email, password: password})
        }).then(function (response) {
            if (response.ok) {
                response.json().then(function (data) {
                    localStorage.setItem('hash', data.hash);
                    getTodos();
                    $("#hello_user").text(email);
                    location.hash = "#/active";

                });
            } else {
                response.json().then(function (data) {
                    alert(data.desc);
                });

            }
        });
    });

    $('.new-todo').keypress(function (e) {
        var key = e.which;
        if (key == 13) {
            var title = $.trim($(this).val());
            if (title != "") {
                $(this).val("");
                fetch("/create_todo.php", {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    method: "POST",
                    body: JSON.stringify({hash: localStorage.getItem('hash'), title: title, is_completed: 0})
                }).then(function (response) {
                    if (response.ok) {
                        response.json().then(function (data) {
                            appendTodo(data.id, title, 0);
                            countTodo();
                        });
                    }
                });
            }
        }
    });

    $('.clear-completed').click(function () {

        fetch("/delete_completed_todos.php", {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            method: "POST",
            body: JSON.stringify({hash: localStorage.getItem('hash')})
        }).then(function (response) {
            if (response.ok) {
                $(".todo-list li.completed").each(function (i, el) {
                    el.remove();
                });
                countTodo();
            }
        });
    });

    $(document).on("click", '.destroy', function (e) {
        var li = $(this).closest("li");
        var id = li.find(".toggle").val();

        fetch("/delete_todo.php", {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            method: "POST",
            body: JSON.stringify({id: id, hash: localStorage.getItem('hash')})
        }).then(function (response) {
            if (response.ok) {
                li.remove();
                countTodo();
            }
        });
    });

    $(document).on("dblclick", '.view', function (e) {
        $(this).closest("li").addClass("editing");
        $(this).closest("li").find(".edit").focus();
        edit = $(this).closest("li").find(".toggle").val();
    });

    $(document).click(function (e) {
        if (edit > 0 && !$(e.target).hasClass('edit')) {
            var text = $(".todo-list li.editing .edit").val();
            updateTodo(edit, text);
        }
    });

    $(document).on("keypress", '.edit', function (e) {
        var key = e.which;
        if (key == 13) {
            updateTodo(edit, $(this).val());
        }
    });

    $(document).on("click", '.toggle-all', function (e) {
        if ($(this).is(":checked")) {
            $.post("/set_all_completed_todo.php", {hash: localStorage.getItem('hash'), is_completed: 1}, function () {
                $(".todo-list li .toggle").prop('checked', true);
                $(".todo-list li").addClass("completed");
                countTodo();
            });
        }
        else {
            $.post("/set_all_completed_todo.php", {hash: localStorage.getItem('hash'), is_completed: 0}, function () {
                $(".todo-list li .toggle").prop('checked', false);
                $(".todo-list li").removeClass("completed");
                countTodo();
            });
        }
    });

    $(document).on("click", '.toggle', function (e) {
        var th = $(this);
        if ($(this).is(":checked")) {
            fetch("/set_completed_todo.php", {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify({id: th.val(), hash: localStorage.getItem('hash'), is_completed: 1})
            }).then(function (response) {
                if (response.ok) {
                    th.closest("li").addClass("completed");
                    countTodo();
                }
            });
        }
        else {
            fetch("/set_completed_todo.php", {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                method: "POST",
                body: JSON.stringify({id: th.val(), hash: localStorage.getItem('hash'), is_completed: 0})
            }).then(function (response) {
                if (response.ok) {
                    th.closest("li").removeClass("completed");
                    countTodo();
                }
            });
        }
    });
    $(window).on('hashchange', function () {
        checkFilter();
    }).trigger("hashchange");
    getTodos();
});


