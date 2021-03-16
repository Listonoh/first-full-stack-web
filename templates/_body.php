<div class="container">
    <h1 class="">Shoping list</h1>

    <table id='table' class="table table-hover table-striped" style="width: 100%">

        <thead class="thead-dark">
            <tr>
                <th style="width: 60%;">item</th>
                <th style="width: 10%;">Amount</th>
                <th style="width: 10%;"></th>
                <th style="width: 10%;"></th>
                <th style="width: 10%;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $obj) { ?>
                <tr>
                    <td><?= htmlspecialchars($obj["name"]) ?></td>
                    <td>
                        <div class="visible amount">
                            <?= htmlspecialchars($obj["amount"]) ?>
                        </div>
                        <input type="number" class="change-amount hidden" name="amount" min="1" max="10000" value="<?= htmlspecialchars($obj["amount"]) ?>">
                    </td>
                    <td>
                        <button class="btn btn-warning btn-switch btn-switch-up">🠕up</button>
                        <button class="btn btn-warning btn-switch btn-switch-down">🠗down</button>

                    </td>
                    <td>
                        <button class="btn btn-primary hidden btn-apply" id="change-<?= htmlspecialchars($obj["id"]) ?>">apply</button>
                        <button class="btn btn-success btn-change visible" id="change-<?= htmlspecialchars($obj["id"]) ?>">change</button>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-delete" id="delete-<?= htmlspecialchars($obj["id"]) ?>">delete</button>
                    </td>

                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<div>
    <tr id="sorting">
        <td>
            <div >
                <button class="btn btn-primary asc btn-sort" id="btn-sortup">Sort asc</button>
                <button class="btn btn-primary desc btn-sort" id="btn-sortdown">Sort desc</button>
            </div>
        </td>
    </tr>
</div>
<span class="input-group-addon"><i class="glyphicon glyphicon-resize-vertical"></i></span>
<div class="container">
    <form method="post" class="form-inline container" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <table class="table" style="width: 100%">
            <tbody>
                <tr>
                    <td style="width: 40%;">
                        <div class="input-group">
                            <span class="input-group-addon">Item</span>
                            <input list="items" class="form-control" name="name" required maxlength="50" id="item" placeholder="">
                            <datalist id="items">
                                <?php foreach ($items as $item) { ?>
                                    <option value="<?= $item["name"] ?>">
                                    <?php } ?>
                            </datalist>
                        </div>
                    </td>
                    <td style="width: 50%;">
                        <div class="input-group">
                            <span class="input-group-addon">Amount</span>
                            <input type="number" class="form-control offset-sm-1" name="amount" min="1" max="10000" id="amount" value="1" pattern="[0-9]+">

                        </div>
                    </td>
                    <td style="width: 10%;">
                        <input type="text" style="display: none;" name="position" value="<?= max(array_column($data, 'position')) + 1 ?>" pattern="[0-9]+">
                        <div class="offset-sm-2 col-sm-1-12 col">
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </td>
                    <td style="width: 0%;"></td>
                </tr>
            </tbody>
        </table>
    </form>
</div>